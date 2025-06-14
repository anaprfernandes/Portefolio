# -*- coding: utf-8 -*-
"""
Created on Tue Dec 17 15:18:00 2024

@author: aprf
"""

# Importar as bibliotecas necessárias
import pandas as pd
from joblib import load  # Para carregar o modelo treinado
from sklearn.metrics import accuracy_score, confusion_matrix, classification_report
from sklearn.ensemble import RandomForestClassifier  # Importar o classificador Random Forest
import seaborn as sns
import matplotlib.pyplot as plt
from sklearn.metrics import ConfusionMatrixDisplay 


# Definir o caminho para o modelo treinado
model_path = "rf_model.joblib"  

# Carregar o modelo treinado com joblib
rf_model = load(model_path)

# Definir o caminho para o novo conjunto de dados (novas características para previsão)
new_data_path = "features_test.csv"  

# Ler o novo conjunto de dados (este deve ter as mesmas colunas que o treinamento)
new_data = pd.read_csv(new_data_path, sep=';')

# Selecionar as mesmas características que foram usadas no treinamento
new_selected_features = new_data[['1_ECDF Percentile_1','1_Centroid','1_Slope',
                          '5_Autocorrelation', '0_Power bandwidth',
                          '5_Median frequency','0_Human range energy', 
                          '5_Median absolute diff', '1_Median absolute diff',
                          '0_Mean diff']]

# Fazer previsões com o modelo treinado
predictions = rf_model.predict(new_selected_features)

# Exibir as previsões
print("Previsões para o novo conjunto de dados:")
print(predictions)

# Opcional: Salvar as previsões em um novo arquivo CSV
new_data['Predictions'] = predictions
new_data.to_csv("rf_predictions_output.csv", sep=';', index=False)

# Obter os rótulos reais (substitua por seus rótulos reais de teste, se disponíveis)
y_test = new_data['Features']

# Criar a matriz de confusão e exibi-la
cm = confusion_matrix(y_test, predictions)
print(cm)

# Exibe a matriz de confusão com um colormap azul
disp = ConfusionMatrixDisplay.from_predictions(y_test, predictions, cmap="Blues")
disp.figure_.suptitle("Confusion Matrix")
plt.xlabel('Predições')
plt.ylabel('Rótulos Reais')
plt.show()

# Exibir o relatório de classificação
report = classification_report(y_test, predictions)
print("Relatório de Classificação:")
print(report)
