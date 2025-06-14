# -*- coding: utf-8 -*-
"""
Project_RPI_BLE.py
Sample program to test BLE communication with the M5Stick.
"""

#!/usr/bin/env python3
import asyncio
import logging
import uuid
import csv
import paho.mqtt.client as mqtt
import threading as th
from datetime import datetime

from bleak import BleakScanner, BleakClient

# Enable debug output
# logging.basicConfig(level=logging.DEBUG)

# This is what identifies your M5Stick. Must be personalized
DEVICE_NAME = "MARTANA"
SERVICE_UUID = uuid.UUID("7f2fa934-4694-4900-b20f-f2da5147a4ef")
CHAR_UUID = uuid.UUID("7f2fa934-4694-4900-b20f-f2da5147a4ef")
MQTT_BROKER = "192.168.1.98"
MQTT_PORT = 1883
MQTT_TOPIC = "AAI/MARTANA/cmd"

# Global variable for the current file name
current_filename = None
stop_acquisition = False  # Flag to indicate when to stop acquisition
mqtt_client = mqtt.Client()

# MQTT Setup
def MQTT_TH(mqtt_client):
    def on_connect(client, userdata, flags, rc):
        if rc == 0:
            print("Connected to MQTT Broker.")
        else:
            print(f"Failed to connect, return code {rc}")

    mqtt_client.on_connect = on_connect
    mqtt_client.connect(MQTT_BROKER, MQTT_PORT, 60)
    mqtt_client.loop_start()
    
# BLE Communication loop
async def run(loop):
    print("Searching devices...")
    devices = await BleakScanner.discover()

    device = list(filter(lambda d: d.name == DEVICE_NAME, devices))
    if len(device) == 0:
        raise RuntimeError(f"Failed to find a device named '{DEVICE_NAME}'")

    address = device[0].address
    print(f"Connecting to the device... (address: {address})")
    while True:  # Retry connection loop
        try:
            async with BleakClient(address, loop=loop) as client:
                print("Connected to device.")
                
                print("Message from the device...")
                value = await client.read_gatt_char(CHAR_UUID)
                print(value.decode())

                print("Sending message to the device...")
                message = bytearray(b"RPI ready")
                await client.write_gatt_char(CHAR_UUID, message, True)
                
                # Receives the BLE data from the M5Stick
                def callback(sender, data):
                    global current_filename, stop_acquisition
                    try: 
                        decoded_data = data.decode()
                        print(f"Received: {decoded_data}")

                        if "Start" in decoded_data:
                            # Create a new CSV file when receiving "Start"
                            current_filename = f"data_{datetime.now().strftime('%Y%m%d_%H%M%S')}.csv"
                            print(f"New acquisition started: {current_filename}")
                            with open(current_filename, "w", newline="") as csvfile:
                                csv_writer = csv.writer(csvfile, delimiter=';')  # Use ; as the delimiter

                        elif "Stop" in decoded_data:
                            print("Acquisition stopped.")
                            stop_acquisition = True  # Set flag to stop acquisition
                        
                        else:
                            # Publish data via MQTT
                            mqtt_client.publish(MQTT_TOPIC, decoded_data)
                            if current_filename:
                                # Split the data into columns and write to the current file
                                with open(current_filename, "a", newline="") as csvfile:
                                    csv_writer = csv.writer(csvfile, delimiter=';')  # Use ; as the delimiter
                                    # Split data by ; if it's expected to be separated
                                    row = decoded_data.split(';')
                                    csv_writer.writerow(row)
                            else:
                                print("No active acquisition. Data not saved.")
                                
                    except Exception as e:
                        print(f"ERROR in callback: {e}")
                
                print("Subscribing to characteristic changes...")
                await client.start_notify(CHAR_UUID, callback)

                # Keep running until the "Stop" signal is received
                while not stop_acquisition:
                    await asyncio.sleep(0.1)

                print("Acquisition completed. Disconnecting...")
                break  # Exit the connection loop to end the program
        except Exception as e:
            print(f"ERROR: Connection failed: {e}. Retrying...")

# Start BLE communication loop
mqtt_client.connect(MQTT_BROKER, 1883, 60)

th_handler = th.Thread(target=MQTT_TH, args=[mqtt_client]) 
th_handler.start()
# Start BLE communication loop
loop = asyncio.get_event_loop()
loop.run_until_complete(run(loop))

