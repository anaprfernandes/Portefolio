# -*- coding: utf-8 -*-
"""
Created on Tue Dec 17 15:18:00 2024

@author: aprf
"""

# importar as bibliotecas necessárias
import pandas as pd
from sklearn.model_selection import StratifiedKFold, cross_val_score
from sklearn.ensemble import RandomForestClassifier  # Importar o classificador Random Forest
from joblib import dump  # Para salvar o modelo treinado

# importar o ficheiro csv
file_path = "all_features_treino.csv"

# ler o ficheiro csv
data = pd.read_csv(file_path, sep=';')

# do ficheiro lido, selecionar as 10 colunas que nos deram no Orange
selected_features = data[['1_ECDF Percentile_1','1_Centroid','1_Slope',
                          '5_Autocorrelation', '0_Power bandwidth',
                          '5_Median frequency','0_Human range energy', 
                          '5_Median absolute diff', '1_Median absolute diff',
                          '0_Mean diff']]

# prepara os dados
X = selected_features
y = data['Features']

# Configurar validação cruzada estratificada com 5 folds
skf = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)

# Criar o classificador Random Forest
rf_model = RandomForestClassifier(n_estimators=100, class_weight='balanced', random_state=42)

# Realizar validação cruzada e calcular os scores
overall_scores = cross_val_score(rf_model, X, y, cv=skf, scoring='accuracy')


# Imprimir os resultados da validação cruzada
print(f"Accuracy média: {overall_scores.mean():.3f}")
print(f"Desvio padrão: {overall_scores.std():.3f}")

# Treinar o modelo final com todos os dados
rf_model.fit(X, y)

# Guardar o modelo treinado usando joblib
dump(rf_model, "rf_model.joblib")