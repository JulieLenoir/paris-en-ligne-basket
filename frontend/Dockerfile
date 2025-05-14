# Utiliser l'image officielle Node.js
FROM node:18

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers de l'application
COPY package.json yarn.lock ./

# Installer les dépendances
RUN yarn install

# Copier le reste des fichiers
COPY . .

# Construire l'application
RUN yarn build

# Exposer le port pour le développement (le cas échéant)
EXPOSE 3000

CMD ["yarn", "start"]
