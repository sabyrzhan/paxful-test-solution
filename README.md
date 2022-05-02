# Paxful Test Solution
## Helm chars installation steps
1. Install prometheus: `helm install prometheus .`
    1. `kubectl port-forward --namespace default svc/prometheus-kube-prometheus-prometheus 9090:9090`
    2. Access in browser at: `http://localhost:9090`
2. Install postgresql-ha: `helm install postgresql-ha .`
3. Install grafana: `helm install grafana .` 
   1. `kubectl port-forward svc/grafana 8080:300`
   2. Access in browser at: `http://localhost:8080`

## Adding secrets
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
