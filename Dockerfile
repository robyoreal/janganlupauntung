FROM node:20-alpine

WORKDIR /app

COPY package*.json ./

RUN npm ci

COPY . .

# Create necessary directories
RUN mkdir -p public/build storage logs tmp

RUN npm run build

EXPOSE 3000

CMD ["npm", "start"]
