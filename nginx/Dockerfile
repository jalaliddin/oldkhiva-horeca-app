# Stage 1: Build Vue frontend
FROM node:20-alpine AS frontend-builder

WORKDIR /app

COPY frontend/package*.json ./
RUN npm ci --quiet

COPY frontend/ .
RUN npm run build

# Stage 2: nginx serving frontend + proxying to PHP-FPM
FROM nginx:alpine

# Frontend static files
COPY --from=frontend-builder /app/dist /usr/share/nginx/html

# Nginx config
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
