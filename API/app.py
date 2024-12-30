from flask import Flask, jsonify, request
import pandas as pd
import numpy as np
import pickle

app = Flask(__name__)

# Charger les données
file_path = 'API/products_adapted.csv'
data = pd.read_csv(file_path)



# Charger les matrices de similarité sauvegardées
with open('API/similarity_matrix_rating.pkl', 'rb') as f:
    similarity_matrix = pickle.load(f)



with open('API/vect_cosine_sim_model.pkl', 'rb') as f:
    cosine_sim = pickle.load(f)



# Fonction pour recommander des produits basés sur la matrice de similarité (évaluations)
def recommend_products_rating_based(product_id, similarity_matrix, top_n=10):
    product_similarities = similarity_matrix[product_id]
    recommended_product_indexes = np.argsort(product_similarities)[::-1]
    return recommended_product_indexes[1:top_n + 1]



# Fonction pour recommander des produits basés sur la similarité cosinus (contenu)
def recommend_products_content_based(product_id, cosine_sim, top_n=10):
    similar_scores = list(enumerate(cosine_sim[product_id]))
    sorted_similar_products = sorted(similar_scores, key=lambda x: x[1], reverse=True)
    recommended_product_indexes = [i[0] for i in sorted_similar_products[1:top_n+1]]
    return recommended_product_indexes



# Fonction pour combiner les recommandations des deux modèles
def combine_recommendations(product_id, similarity_matrix, cosine_sim, top_n=20):
   
    recommended_by_rating = recommend_products_rating_based(product_id, similarity_matrix, top_n=10)
    recommended_by_content = recommend_products_content_based(product_id, cosine_sim, top_n=10)
    combined_recommendations = list(set(recommended_by_rating) | set(recommended_by_content))
    
    return combined_recommendations[:top_n]



@app.route('/recommend', methods=['GET'])
def recommend():
    product_id = request.args.get('product_id', default=None)

    if product_id is not None:
        try:
            product_id = int(product_id)
            print(f"Received product_id: {product_id}")
        except ValueError:
            return "Le paramètre 'product_id' doit être un entier valide", 400
    else:
        return "Paramètre 'product_id' manquant", 400

        

    # Obtenir les produits recommandés combinés (20 produits)
    recommended_indexes = combine_recommendations(product_id, similarity_matrix, cosine_sim, top_n=20)
    print(f"Recommended indexes: {recommended_indexes}")

    # Extraire les données des produits recommandés
    recommended_products_data = data.iloc[recommended_indexes]
    recommended_products = []
    for index, row in recommended_products_data.iterrows():
        recommended_products.append({
            'id': row['id'],
            'libelle': row['libelle'],
            'prix': row['prix'],
            'image': row['image']
        })

    print(f"Recommended products: {recommended_products}")
    return jsonify(recommended_products)

if __name__ == '__main__':
    app.run(debug=True)
