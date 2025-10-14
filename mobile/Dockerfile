# Multi-stage build for React Native mobile app
FROM node:18-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    bash \
    openjdk11 \
    android-tools \
    android-platform-tools

# Set working directory
WORKDIR /app

# Copy package files
COPY package.json package-lock.json ./

# Install dependencies
RUN npm ci --only=production

# Copy source code
COPY . .

# Build the app
RUN npm run build:android

# Production stage
FROM node:18-alpine AS production

# Install system dependencies
RUN apk add --no-cache \
    curl \
    bash \
    openjdk11

# Set working directory
WORKDIR /app

# Copy built application
COPY --from=base /app .

# Expose port
EXPOSE 3000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:3000/health || exit 1

# Start the application
CMD ["npm", "start"]





