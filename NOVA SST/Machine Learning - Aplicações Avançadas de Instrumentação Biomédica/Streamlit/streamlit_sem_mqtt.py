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
FEATURE_OUTPUT_FOLDER = "Final"
SELECTED_FEATURES = [
    '1_ECDF Percentile_1', '1_Centroid', '1_Slope',
    '5_Autocorrelation', '0_Power bandwidth',
    '5_Median frequency', '0_Human range energy', 
    '5_Median absolute diff', '1_Median absolute diff',
    '0_Mean diff'
]

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

# Função para carregar o modelo

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

# Inicia o histórico na sessão
if "history" not in st.session_state:
    st.session_state["history"] = []

# Função para atualizar o feedback da classificação
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

        if feedback_counts.get("NÃO", 0) >= feedback_counts.get("SIM", 0):
            st.sidebar.warning("O classificador necessita de ajustes.")
        else:
            st.sidebar.success("Boa classificação!")
    else:
        st.sidebar.write("Nenhum feedback registado ainda.")

# Configuração da interface do Streamlit
st.set_page_config(page_title="Classificação de Movimentos", layout="centered")
st.title("Sistema de Classificação de Movimentos")

st.subheader("Bem-vindo ao nosso Projeto")
st.text("Para testar o nosso modelo, realize um dos seguintes movimentos: Andar, Sentar ou Levantar, com o sensor junto ao peito")


if st.button("Iniciar Programa"):
    #mqtt_client = st.session_state["mqtt_client"]
    #st.session_state.mqttThread.start()  # Starts the thread that controls MQTT
    #mqtt_client.publish(MQTT_TOPIC_PUB, "Pronto para iniciar o programa")
    st.write("Pronto para iniciar o programa")
    
    
# Botão para iniciar a leitura de dados com M5 Stick
if st.button("Iniciar Leitura de Dados com M5 Stick"):
   # mqtt_client=st.session_state["mqtt_client"] 
    #mqtt_client.publish(MQTT_TOPIC_PUB, "Leitura Iniciada")
    result = start_project_rpi_ble()
    st.write(result)
    # Publicar previsão no MQTT

    
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

# Seção para avaliação do classificador
if st.session_state["history"]:
    with st.expander("Avaliar Classificador"):
        st.write("Forneça feedback sobre a última classificação.")
        if st.button("Classificação Correta"):
            update_classification_feedback(correct=True)
            st.success("Ótimo!")

        if st.button("Classificação Incorreta"):
            update_classification_feedback(correct=False)
            st.error("Upss! Parece ter havido um erro.")

# Botão para exibir o histórico de movimentos
if st.session_state["history"]:
    if st.button("Consultar Histórico de Movimentos"):
        if st.session_state["history"]:
            st.write("Histórico de Movimentos")
            history_df = pd.DataFrame(st.session_state["history"])
            history_df = history_df.rename(columns={"filename": "Arquivo", "prediction": "Movimento", "timestamp": "Horário", "correct": "Avaliação"})
            st.dataframe(history_df)
        else:
            st.write("Nenhum movimento foi classificado ainda.")

# Análises Estatísticas
if st.session_state["history"]:
    if st.button("Qual foi o movimento mais frequente?"):
        if len(st.session_state["history"]) < 2:
            st.write("São necessários pelo menos dois movimentos diferentes classificados para calcular o tempo.")
        else:
            history_df = pd.DataFrame(st.session_state["history"])
            history_df['timestamp'] = pd.to_datetime(history_df['timestamp'])
            history_df = history_df.sort_values(by="timestamp")

            total_time_by_movement = {}

            for i in range(len(history_df) - 1):
                current_row = history_df.iloc[i]
                next_row = history_df.iloc[i + 1]

                duration = next_row["timestamp"] - current_row["timestamp"]
                current_movement = current_row["prediction"]

                if current_movement not in total_time_by_movement:
                    total_time_by_movement[current_movement] = 0

                total_time_by_movement[current_movement] += duration.total_seconds()

            last_row = history_df.iloc[-1]
            last_movement = last_row["prediction"]
            last_time = datetime.now() - last_row["timestamp"]

            if last_movement not in total_time_by_movement:
                total_time_by_movement[last_movement] = 0
            total_time_by_movement[last_movement] += last_time.total_seconds()

            time_df = pd.DataFrame(list(total_time_by_movement.items()), columns=["Movimento", "Tempo Total (s)"])

            fig, ax = plt.subplots(figsize=(8, 6))
            sns.barplot(x="Movimento", y="Tempo Total (s)", data=time_df, ax=ax)
            ax.set_xlabel("Movimento")
            ax.set_ylabel("Tempo Total (s)")
            ax.set_title("Tempo Total por Movimento")

            for index, row in time_df.iterrows():
                ax.text(index, row["Tempo Total (s)"], f"{row['Tempo Total (s)']:.1f}", color="black", ha="center")

            st.pyplot(fig)

            most_time_movement = time_df.loc[time_df["Tempo Total (s)"].idxmax(), "Movimento"]

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