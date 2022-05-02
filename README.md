# Paxful Test Solution
## Overview
The solution consists from:
1. Helm charts of PostgreSQL, Grafana, Prometheus and WebApp itself.
2. There are 2 docker-compose files:
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
