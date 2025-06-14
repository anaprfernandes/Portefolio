

# -*- coding: utf-8 -*-
"""
Created on Thu Jan  9 10:19:19 2025
@author: aprfe
"""

import streamlit as st
import pandas as pd

import os
import threading as th

from joblib import load
import tsfel
import paho.mqtt.client as mqtt
from streamlit.runtime.scriptrunner import add_script_run_ctx
from streamlit_autorefresh import st_autorefresh
from datetime import datetime
import matplotlib.pyplot as plt
import seaborn as sns
import subprocess
import json
global mqtt_client 

# Caminho do modelo treinado
MODEL_PATH = "rf_model.joblib"
CURRENT_DIR = os.path.dirname(os.path.realpath(__file__)) 
#a localização do ficheiro que vamos ler está na mesma pasta que este ficheiro
FEATURE_OUTPUT_FOLDER = "Final"
SELECTED_FEATURES = [
    '1_ECDF Percentile_1', '1_Centroid', '1_Slope',
    '5_Autocorrelation', '0_Power bandwidth',
    '5_Median frequency', '0_Human range energy', 
    '5_Median absolute diff', '1_Median absolute diff',
    '0_Mean diff'
] #features ditas pelo orange

# MQTT Configuration
BROKER = '192.168.1.98'
MQTT_PORT = 1883
MQTT_TOPIC_SUB = 'AAI/MARTANA/cmd'
MQTT_TOPIC_PUB = 'AAI/MARTANA/cmd'

# Inicializar cliente MQTT
if "mqtt_client" not in st.session_state:
    st.session_state["mqtt_client"] = mqtt.Client()
    st.session_state["mqtt_messages"] = []  # Para armazenar mensagens recebidas
    st.session_state["history"]=[]

# Função para configurar o cliente MQTT
def setup_mqtt():
    def on_connect(client, userdata, flags, rc):
        if rc == 0:
            print("MQTT conectado com sucesso.")
            client.subscribe(MQTT_TOPIC_SUB)  # Subscrição ao tópico de previsões
        else:
            print(f"Falha ao conectar no MQTT. Código: {rc}")

    def on_message(client, userdata, msg):
        # Processar mensagens 
        try:
            message = json.loads(msg.payload.decode("utf-8"))
            prediction = message.get("Prediction", "Desconhecido")
            timestamp = message.get("Timestamp", datetime.now().isoformat())
            
            # Atualizar o histórico
            st.session_state["history"].append({
                "timestamp": timestamp,
                "prediction": prediction,
                "source": "MQTT"
            })

        except json.JSONDecodeError:
            print("Erro ao decodificar JSON recebido via MQTT.")
        except Exception as e:
            print(f"Erro ao processar mensagem MQTT: {e}")

    # Configurar cliente MQTT
    mqtt_client = st.session_state["mqtt_client"]
    mqtt_client.on_connect = on_connect
    mqtt_client.on_message = on_message

    # Conectar ao broker e subscrever ao tópico
    mqtt_client.connect(BROKER, MQTT_PORT, 60)
    mqtt_thread = th.Thread(target=mqtt_client.loop_forever)
    mqtt_thread.daemon = True
    mqtt_thread.start()

setup_mqtt()
global mqtt_client 
# Função para publicar previsões no MQTT
def publish_prediction(prediction, timestamp):
    mqtt_client= st.session_state["mqtt_client"]
    message = {
        "Movimento": prediction,
        "Momento": timestamp
    }
    mqtt_client.publish(MQTT_TOPIC_PUB, json.dumps(message))
    
    
def start_project_rpi_ble():
    try:
        # Executar o script Project_RPI_BLE_streamlit2.py como um subprocesso
        result = subprocess.run(['python', 'Project_RPI_BLE_streamlit2.py'], capture_output=True, text=True)

        # Verificar se o script foi executado com sucesso
        if result.returncode == 0:
            return f"Leitura de dados iniciada com sucesso. Pronto para classificar movimento"
        else:
            return f"Erro ao iniciar a leitura de dados. Erro: {result.stderr}"
    
    except Exception as e:
        return f"Ocorreu um erro ao tentar iniciar o script: {str(e)}"
# Função para extrair características do último arquivo CSV criado

def extract_features_from_last_csv(input_folder):
    files = [f for f in os.listdir(input_folder) if f.endswith('.csv')]
    if not files:
        return None
    latest_file = max(files, key=lambda f: os.path.getmtime(os.path.join(input_folder, f)))
    file_path = os.path.join(input_folder, latest_file)

    # Ler os dados e extrair as características
    data = pd.read_csv(file_path, header=None, sep=';')
    signal = data.iloc[:, :6]
    cfg = tsfel.get_features_by_domain()
    features = tsfel.time_series_features_extractor(cfg, signal, verbose=0)
    features['filename'] = latest_file

    return features

# Função para carregar o modelo compatível com a nova versão
def load_model(model_path):
    try:
        model = load(model_path)
    except AttributeError as e:
        raise RuntimeError("Erro de compatibilidade do modelo. Certifique-se de alinhar versões do scikit-learn.") from e
    return model

# Função para classificar os dados extraídos
def classify_data(features, model):
    selected_features = features[SELECTED_FEATURES].copy()
    selected_features = selected_features.fillna(0)
    predictions = model.predict(selected_features)
    features['Prediction'] = predictions
    return features


#ADICIONAR MOVIMENTOS AO HISTÓRICO
# Inicia o histórico na sessão
if "history" not in st.session_state:
    st.session_state["history"] = []

#ESTATÍSTICAS
def update_classification_feedback(correct):
    if "history" in st.session_state and st.session_state["history"]:
        if isinstance(st.session_state["history"][-1], dict):
            st.session_state["history"][-1]["correct"] = "SIM" if correct else "NÃO"
        else:
            st.error("Último item no histórico não é válido. Verifique os dados.")

# Função para exibir estatísticas do classificador
def display_classifier_statistics():
    if "history" in st.session_state and st.session_state["history"]:
        history_df = pd.DataFrame(st.session_state["history"])
        feedback_counts = history_df["correct"].value_counts()

        # Plot do gráfico
        fig, ax = plt.subplots(figsize=(6, 4))
        sns.barplot(x=feedback_counts.index, y=feedback_counts.values, ax=ax)
        ax.set_xlabel("Feedback")
        ax.set_ylabel("Quantidade")
        ax.set_title("Estatísticas de Classificação")
        st.sidebar.pyplot(fig)

        # Mensagem com base nos resultados
        if feedback_counts.get("NÃO", 0) >= feedback_counts.get("SIM", 0):
            st.sidebar.warning("O classificador necessita de ajustes.")
        else:
            st.sidebar.success("Boa classificação!")
    else:
        st.sidebar.write("Nenhum feedback registado ainda.")
        
#INTERFACE COM O UTILIZADOR
# Streamlit: Configuração da interface
st.set_page_config(page_title="Classificação de Movimentos", layout="centered")
st.title("Sistema de Classificação de Movimentos")

st.subheader("Bem-vindo ao nosso Projeto")
st.text("Para testar o nosso modelo pedimos que realize um dos seguintes movimentos: Andar, Sentar ou Levantar, com o sensor junto ao peito")

 
if st.button("Iniciar Programa"):
    mqtt_client = st.session_state["mqtt_client"]
    st.session_state.mqttThread.start()  # Starts the thread that controls MQTT
    mqtt_client.publish(MQTT_TOPIC_PUB, "Pronto para iniciar o programa")


# Botão para iniciar a leitura de dados com M5 Stick
if st.button("Iniciar Leitura de Dados com M5 Stick"):
    mqtt_client=st.session_state["mqtt_client"] 
    mqtt_client.publish(MQTT_TOPIC_PUB, "Leitura Iniciada")
    result = start_project_rpi_ble()
    st.write(result)
    # Publicar previsão no MQTT
    

#EVENTUALMENTE METER AQUI UMA CONDIÇÃO PARA SO APARECER O BOTÃO DE CLASSIFICAR QUANDO ALGUM MOVIMENTO FOR LIDO
# Botão para processar o último arquivo CSV e fazer a classificação
if st.button("Classificar Último Movimento"):
    features_df = extract_features_from_last_csv(CURRENT_DIR)

    if features_df is None:
        st.write("Nenhum arquivo CSV encontrado na pasta.")
    else:
        rf_model = load_model(MODEL_PATH)
        classified_df = classify_data(features_df, rf_model)

        # Ler o movimento classificado e o horário
        classified_movement = classified_df['Prediction'][0]
        current_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

        # Adicionar a predição e horário ao histórico
        st.session_state["history"].append({
            "filename": classified_df['filename'][0],
            "timestamp": current_time,
            "prediction": classified_movement,
        })

        # Publicar previsão no MQTT
        publish_prediction(classified_movement, current_time)

        st.success(f"Movimento classificado: {classified_movement}")
        # Exibir o vídeo relacionado ao movimento classificado
        if classified_movement.lower() == 'sentar':
            st.markdown("""
                 <iframe src="https://giphy.com/embed/11ewTKMijWxd4s" width="480" height="370" frameBorder="0" class="giphy-embed" allowFullScreen></iframe>
                 <p><a href="https://giphy.com/gifs/bear-sit-11ewTKMijWxd4s">via GIPHY</a></p>
                 """, unsafe_allow_html=True)
        elif classified_movement.lower() == 'levantar':
            st.markdown("""
                <iframe src="https://giphy.com/embed/OaK4VwfDTspYCyC2f8" width="480" height="269" frameBorder="0" class="giphy-embed" allowFullScreen></iframe>
                <p><a href="https://giphy.com/gifs/nbc-season-10-theblacklist-OaK4VwfDTspYCyC2f8">via GIPHY</a></p>
                """, unsafe_allow_html=True)
        elif classified_movement.lower() == 'andar':
            st.markdown("""
                <iframe src="https://giphy.com/embed/QpWDP1YMziaQw" width="480" height="480" frameBorder="0" class="giphy-embed" allowFullScreen></iframe>
                <p><a href="https://giphy.com/gifs/animated-the-beatles-QpWDP1YMziaQw">via GIPHY</a></p>
                """, unsafe_allow_html=True)

     
#Secção para avaliação do classificador
#Nós só queremos que ele peça avaliação depois de termos classificado algo
if st.session_state["history"]:  # Verifica se há algum histórico
    with st.expander("Avaliar Classificador"):
        st.write("Forneça feedback sobre a última classificação.")
        if st.button("Classificação Correta"):
            update_classification_feedback(correct=True)
            mqtt_client=st.session_state["mqtt_client"] 
            mqtt_client.publish(MQTT_TOPIC_PUB, "Classificação Correta")
            st.success("Ótimo!")

        if st.button("Classificação Incorreta"):
            update_classification_feedback(correct=False)
            mqtt_client=st.session_state["mqtt_client"] 
            st.error("Upss! Parece ter havido um erro..")
            mqtt_client.publish(MQTT_TOPIC_PUB, "Classificação Incorreta")
            
            
# Botão para exibir o histórico de movimentos
if st.session_state["history"]:  # Verifica se há algum histórico
#Nós só queremos que ele peça avaliação depois de termos classificado algo
    if st.button("Consultar Histórico de Movimentos"):
        if st.session_state["history"]:
            st.write("Histórico de Movimentos")
            history_df = pd.DataFrame(st.session_state["history"])
            history_df = history_df.rename(columns={"filename": "Arquivo", "prediction": "Movimento", "timestamp": "Horário", "correct": "Avaliação"}) #Aqui mostra a informação toda sobre a classificação 
            st.dataframe(history_df)
            predictions_list = json.dumps(st.session_state["history"])
            # Publica a lista no tópico de previsões
            mqtt_client=st.session_state["mqtt_client"] 
            mqtt_client.publish(MQTT_TOPIC_PUB, predictions_list)
        else:
            st.write("Nenhum movimento foi classificado ainda.")

#ANÁLISES ESTATÍSTICAS        
# Botão para calcular tempo entre movimentos diferentes e exibir gráfico de frequências
if st.session_state["history"]:  # Verifica se há algum histórico
#Nós só queremos que ele peça avaliação depois de termos classificado algo
    if st.button("Qual foi o movimento mais frequente?"):
        if len(st.session_state["history"]) < 2:
            st.write("São necessários pelo menos dois movimentos diferentes classificados para calcular o tempo.")
        else:
            history_df = pd.DataFrame(st.session_state["history"])
            history_df['timestamp'] = pd.to_datetime(history_df['timestamp'])  # Converter para datetime
            history_df = history_df.sort_values(by="timestamp")  # Garantir ordem cronológica
    
            # Iniciar dicionário para acumular o tempo total por movimento
            total_time_by_movement = {}
    
            # Calcular tempos entre movimentos consecutivos e acumular
            for i in range(len(history_df) - 1):
                current_row = history_df.iloc[i]
                next_row = history_df.iloc[i + 1]
    
                # Verificar se o movimento atual é o mesmo ou diferente do próximo
                duration = next_row["timestamp"] - current_row["timestamp"]
                current_movement = current_row["prediction"]
                #Ve se houve mudança no movimento predicted
                if current_movement not in total_time_by_movement:
                    total_time_by_movement[current_movement] = 0  # Iniciar tempo para o movimento
    
                # Somar tempo ao movimento atual
                total_time_by_movement[current_movement] += duration.total_seconds()
    
            # Parar o tempo do último movimento classificado
            #Isto é necessário porque se não o ultimo movimento fica a contar infinitamente até que classifiquemos um novo
            last_row = history_df.iloc[-1]
            last_movement = last_row["prediction"]
            last_time = datetime.now() - last_row["timestamp"]
    
            if last_movement not in total_time_by_movement:
                total_time_by_movement[last_movement] = 0  # Iniciar tempo para o movimento
            total_time_by_movement[last_movement] += last_time.total_seconds() #Vai sempre incrementando 
    
            # Converter para DataFrame para fazer o plot
            time_df = pd.DataFrame(list(total_time_by_movement.items()), columns=["Movimento", "Tempo Total (s)"])
    
            # Plot do gráfico de barras do tempo total por movimento
            fig, ax = plt.subplots(figsize=(8, 6))
            sns.barplot(x="Movimento", y="Tempo Total (s)", data=time_df, ax=ax)
            ax.set_xlabel("Movimento")
            ax.set_ylabel("Tempo Total (s)")
            ax.set_title("Tempo Total por Movimento")
    
            # Adicionar valores diretamente nas barras para ficar bonitinho 
            for index, row in time_df.iterrows():
                ax.text(index, row["Tempo Total (s)"], f"{row['Tempo Total (s)']:.1f}", color="black", ha="center")
    
            # Exibir o gráfico no Streamlit
            st.pyplot(fig)

            # Determinar o movimento com mais tempo acumulado
            most_time_movement = time_df.loc[time_df["Tempo Total (s)"].idxmax(), "Movimento"] #procura o max no dataframe com os tempos de cada movimento

            # Mensagem personalizada com base no movimento que fez durante mais tempo
            if most_time_movement.lower() == 'andar':
                st.success("Boa! O senhor teve uma boa atividade hoje!")
            else:
                st.info(f"O movimento mais frequente foi: {most_time_movement}. Tem de se mexer mais que esse colesterol não perdoa!")


    
#ABA LATERAL    
sidebar_option = st.sidebar.selectbox(
    "Escolha uma secção:",
    ["Apresentação do Projeto", "Estatísticas", "Referências"]
)

if sidebar_option == "Apresentação do Projeto":
    st.sidebar.title("Apresentação do Projeto")
    st.sidebar.write("""
    Este projeto foi desenvolvido no âmbito do **Aplicações Avançadas de Engenharia Biomédica**. 
    Através deste programa de Machine Learning, pretendemos monitorizar os movimentos de **Andar**, **Levantar** e **Sentar**. 
    O objetivo é avaliar as taxas de sedentarismo na **população idosa**, contribuindo para o seu bem-estar e qualidade de vida.
    """)

    st.sidebar.image("homepage.jpg", caption="Exemplo de Movimentos: Andar, Levantar, Sentar", use_container_width=True)

    st.sidebar.write("""
    Imagine que é um avô de 70 anos, que vive sozinho, sem acompanhamento constante dos filhos ou netos. 
    Este modelo funciona como uma ferramenta que pode ajudar a melhorar a **monitorização da sua saúde** e a enviar essa informação para os seus cuidadores, promovendo a sua **independência**.
    """)
    
elif sidebar_option == "Estatísticas":
    st.sidebar.title("Estatísticas do Classificador")
    #Botão para exibir estatísticas do classificador
    st.sidebar.write("### Matriz de Confusão do Modelo:")
    st.sidebar.text("Apresentamos abaixo a matriz de confusão gerada com base nos resultados do teste do modelo.")
    st.sidebar.image("matriz final.png", caption="Monitorização de Movimentos", use_container_width=True)
    st.sidebar.text("""Este classificador foi projetado com base nas 10 melhores features retiradas de um conjunto de treino de 40 amostras para cada movimento.
                    O classificador utilizado foi o Random Forest por apresentar uma boa Accuracy e relação de Area under the curve.
                    """)
    if st.sidebar.button("Exibir Estatísticas do Classificador"): #Ao carregar no botão aparece o grafico das avaliações
        display_classifier_statistics()

elif sidebar_option == "Referências":
    st.sidebar.title("Referências")

    # Mensagens a serem publicadas
    st.sidebar.header("Alunas responsáveis")
    st.sidebar.text("Ana Fernandes 59995 MIEB")
    st.sidebar.text("Marta Simão 60666 MIEB")
    
    st.sidebar.header("Professores responsáveis")
    st.sidebar.text("Hugo Gamboa")
    st.sidebar.text("Pedro Vieira")
    
# Verificação e inicialização da thread MQTT
if 'mqttThread' not in st.session_state:
    # Criação do cliente MQTT e da thread
    print('session state')
    st.session_state.mqttClient = mqtt.Client()
    st.session_state.mqttThread = th.Thread(target=setup_mqtt, args=[st.session_state.mqttClient])
    
    # Adicionando a thread ao contexto do Streamlit
    add_script_run_ctx(st.session_state.mqttThread)
    

elif not st.session_state.mqttThread.is_alive():
    # Inicia a thread novamente apenas se ela não estiver ativa
    st.session_state.mqttThread = th.Thread(target=setup_mqtt, args=[st.session_state.mqttClient])
    add_script_run_ctx(st.session_state.mqttThread)
    st.session_state.mqttThread.start()
    

