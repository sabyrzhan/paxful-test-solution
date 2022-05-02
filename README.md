# Paxful Test Solution
## Overview
The solution is the webapp written in Laravel PHP framework with helm charts.
Helm charts are located at `helm` folder with following contents:
1. `grafana` - Grafana helm chart from Bitnami
2. `kube-prometheus` - Prometheus helm chart from Bitnami
3. `postgresql-ha` - PostgreSQL-HA with RepMgr from Bitnami
4. `webapp` - this web application's helm chart
5. There are 2 docker-compose files:
   1. `docker-compose.yml` - for local development
   2. `docker-compose-test.yml` - for testing integration with DB and Mail services

## Helm chars installation steps
1. Install prometheus: `helm install prometheus .`
    1. `kubectl port-forward --namespace default svc/prometheus-kube-prometheus-prometheus 9090:9090`
    2. Access in browser at: `http://localhost:9090`
2. Install postgresql-ha: `helm install postgresql-ha .`
3. Install grafana: `helm install grafana .` 
   1. `kubectl port-forward svc/grafana 8080:300`
   2. Access in browser at: `http://localhost:8080`

## Secrets
### Database secret
Database secret `db_password` with key `password` must be added to secret.
1. Create secret:
```
kubectl create secret generic db-password \
  --from-literal=password=PASSWORD
```
2. Verify secret:
```
kubectl get secrets
```

### App secret
Since webapp was built using Laravel, it requires secret in base64 format.
The create app key secret:
1. Generate key:
```
php artisan key:generate --show
```
2. Create secret and `key`:
```
kubectl create secret generic app-key \
  --from-literal=key=GENERATED_KEY
```
2. Verify secret:
```
kubectl get secrets
```

## Email UI
To check sent emails:
1. `kubectl port-forward svc/paxful-email-service 8025:8025`
2. Access in browser: `http://localhost:8025`

## Grafana UI
If you added Prometheus and Grafana charts then you should import
PostgreSQL dashboard with ID: 9628

## Database schema
Schema data is stored in `schema.sql`. Import it into the clean database.

## Local development
Before starting to develop do the following:
1. Copy `.env.example` to `.env` file and specify DB and mail parameters
2. Start containers with `docker-compose up`
3. Migrate DB schema from `schema.sql` manually
