//Lab6_M5Stick_BLE.ino
//Sample program, to run in the M5Stick, to test BLE communication with the RPI
//v100 - PV - AAI(23/24)

// button A -  send message with all MPU data
// button B - starts continuous acquisition at 10 Hz of accelerometers

#include <M5StickCPlus.h>
#include <BLEDevice.h>
#include <BLEUtils.h>
#include <BLEServer.h>
#include <BLE2902.h>


// See the following for generating UUIDs version 4:
// https://www.uuidgenerator.net/
//this is what identifies your M5Stick. Must be personalized
#define DEVICE_NAME         "MARTANA"
#define SERVICE_UUID        "7f2fa934-4694-4900-b20f-f2da5147a4ef"
#define CHARACTERISTIC_UUID "7f2fa934-4694-4900-b20f-f2da5147a4ef"

BLEServer* pServer = NULL;
BLECharacteristic* pCharacteristic = NULL;
bool deviceConnected = false;


//Globals Variables
float accX = 0.0F;
float accY = 0.0F;
float accZ = 0.0F;

float gyroX = 0.0F;
float gyroY = 0.0F;
float gyroZ = 0.0F;


bool acq_flag = false;
long acq_time = millis();


//Callback on connection
class MyServerCallbacks: public BLEServerCallbacks {
    void onConnect(BLEServer* pServer) {
      M5.Lcd.println("BLE connect");
      deviceConnected = true;
    };

    void onDisconnect(BLEServer* pServer) {
      M5.Lcd.println("BLE disconnect");
      deviceConnected = false;
    }
};

//Callback to read and write messages
class MyCallbacks: public BLECharacteristicCallbacks {
  void onRead(BLECharacteristic *pCharacteristic) {
    M5.Lcd.println("Tx to RPI");
    pCharacteristic->setValue("Message from M5Stick");
  }
  
  void onWrite(BLECharacteristic *pCharacteristic) {
    M5.Lcd.println("Rx from RPI");
    std::string value = pCharacteristic->getValue();
    M5.Lcd.println(value.c_str());
  }
};

//setups the necessary hardware
void setup() {
  Serial.begin(115200);
  M5.begin();
  M5.Lcd.println("BLE start.");

  BLEDevice::init(DEVICE_NAME);
  BLEServer *pServer = BLEDevice::createServer();
  pServer->setCallbacks(new MyServerCallbacks());
  BLEService *pService = pServer->createService(SERVICE_UUID);
  pCharacteristic = pService->createCharacteristic(
                                         CHARACTERISTIC_UUID,
                                         BLECharacteristic::PROPERTY_READ |
                                         BLECharacteristic::PROPERTY_WRITE |
                                         BLECharacteristic::PROPERTY_NOTIFY |
                                         BLECharacteristic::PROPERTY_INDICATE
                                       );
  pCharacteristic->setCallbacks(new MyCallbacks());
  pCharacteristic->addDescriptor(new BLE2902());

  pService->start();
  BLEAdvertising *pAdvertising = pServer->getAdvertising();
  pAdvertising->start();
  M5.Lcd.println("BLE running.");

  M5.Lcd.println("IMU Starting");
  M5.Imu.Init(); 
  M5.Lcd.println("IMU Ready.");
}

//Main Loop
void loop() {
  char buf[80];  // Buffer to store sensor data
  
  if (deviceConnected) {
    // If button A is pressed, toggle acquisition
    if (M5.BtnA.wasPressed()) {
      acq_flag = !acq_flag; // Toggle acquisition flag
      if (acq_flag) {
        // Start acquisition
        pCharacteristic->setValue("Acquisition Start");
        pCharacteristic->notify();
        M5.Lcd.println("Acquisition Start");
        acq_time = millis();
      } else {
        // Stop acquisition
        pCharacteristic->setValue("Acquisition Stop");
        pCharacteristic->notify();
        M5.Lcd.println("Acquisition Stop");
      }
    }

    // If button B is pressed, read and send both accelerometer and gyroscope data
    if (M5.BtnB.wasPressed()) {
      M5.Lcd.println("Button B pressed!");
      pCharacteristic->setValue("Button B pressed!");
      pCharacteristic->notify();

      // Read data from gyroscope and accelerometer
      M5.IMU.getGyroData(&gyroX, &gyroY, &gyroZ);
      M5.IMU.getAccelData(&accX, &accY, &accZ);

      // Format and display/send the data
      snprintf(buf, sizeof(buf), "Gyro: %6.2f; %6.2f; %6.2f | Acc: %6.2f; %6.2f; %6.2f",
               gyroX, gyroY, gyroZ, accX, accY, accZ);
      M5.Lcd.println(buf);               // Display on LCD
      pCharacteristic->setValue(buf);    // Send via BLE
      pCharacteristic->notify();
    }

    // Continuous acquisition at 10 Hz
    if (acq_flag && millis() - acq_time >= 100) {
      M5.IMU.getAccelData(&accX, &accY, &accZ);
      M5.IMU.getGyroData(&gyroX, &gyroY, &gyroZ);

      snprintf(buf, sizeof(buf), "%6.2f; %6.2f; %6.2f; %6.2f; %6.2f; %6.2f", 
       accX, accY, accZ, gyroX, gyroY, gyroZ);

      M5.Lcd.println(buf);              // Display on LCD
      pCharacteristic->setValue(buf);   // Send via BLE
      pCharacteristic->notify();x

      acq_time = millis();
    }
  }
  M5.update();
}
