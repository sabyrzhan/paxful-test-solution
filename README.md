# Paxful Test Solution
## Helm chars installation steps
1. Install prometheus: `helm install prometheus .`
    1. `kubectl port-forward --namespace default svc/prometheus-kube-prometheus-prometheus 9090:9090`
    2. Access in browser at: `http://localhost:9090`
2. Install postgresql-ha: `helm install postgresql-ha .`
3. Install grafana: `helm install grafana .` 
   1. `kubectl port-forward svc/grafana 8080:300`
   2. Access in browser at: `http://localhost:8080`