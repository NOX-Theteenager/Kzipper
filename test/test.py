import os

def creer_dossier_avec_fichiers(racine, structure):
    """
    Crée une structure de dossiers et de fichiers en fonction des paramètres spécifiés.
    
    :param racine: Chemin du dossier principal.
    :param structure: Dictionnaire représentant la structure des sous-dossiers et fichiers.
    """
    os.makedirs(racine, exist_ok=True)
    
    for sous_dossier, fichiers in structure.items():
        chemin_sous_dossier = os.path.join(racine, sous_dossier)
        os.makedirs(chemin_sous_dossier, exist_ok=True)
        
        for nom_fichier, contenu in fichiers.items():
            chemin_fichier = os.path.join(chemin_sous_dossier, nom_fichier)
            with open(chemin_fichier, 'w', encoding='utf-8') as fichier:
                fichier.write(contenu)

# Définition de la structure des dossiers et des fichiers
structure_dossiers = {
    "dossier1": {
        "fichier1.txt": "Ceci est le contenu du fichier 1 dans dossier 1.",
        "fichier2.txt": "Contenu simple pour le fichier 2 dans dossier 1."
    },
    "dossier2": {
        "fichier1.txt": "Texte avec des répétitions : ABCABCABCABC.",
        "fichier2.txt": "Une phrase un peu plus longue pour tester.",
        "fichier3.txt": "1234567890" * 10  # Données numériques répétées
    },
    "dossier3": {
        "fichier_unique.txt": "Un seul fichier dans ce dossier."
    }
}

# Chemin où les fichiers seront créés
chemin_racine = "dossier_test_compression"

# Création de la structure
creer_dossier_avec_fichiers(chemin_racine, structure_dossiers)

print(f"La structure de fichiers a été créée dans le dossier : {chemin_racine}")
