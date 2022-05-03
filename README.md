# 👨‍💻 📝 📊 Paxful Test Solution
## Overview
The solution is the webapp written in Laravel PHP framework with helm charts.
Helm charts are located at `helm` folder with following contents:
1. `grafana` - Grafana helm chart from [Bitnami](https://github.com/bitnami/charts/tree/master/bitnami/grafana)
2. `kube-prometheus` - Prometheus helm chart from [Bitnami](https://github.com/bitnami/charts/tree/master/bitnami/kube-prometheus)
3. `postgresql-ha` - PostgreSQL-HA with RepMgr from [Bitnami](https://github.com/bitnami/charts/tree/master/bitnami/postgresql-ha)
4. `webapp` - this web application's helm chart
5. There are 2 docker-compose files:
   1. `docker-compose.yml` - for local development
   2. `docker-compose-test.yml` - for testing integration with DB and Mail services


## Application test requirement
1. It responds to the URL like `http://host/?n=x` and returns n*n.
2. It responds to the URL `http://host/blacklisted` with conditions:
    * return error code 444 to the visitor
    * block the IP of the visitor
    * send an email with IP address to `test@domain.com`
    * insert into PostgreSQL table information: path, IP address of the visitor and datetime when he got blocked

## Helm chars installation steps
1. Add secrets as descibed below
2. Install prometheus: `helm install prometheus .`
    1. `kubectl port-forward --namespace default svc/prometheus-kube-prometheus-prometheus 9090:9090`
    2. Access in browser at: `http://localhost:9090`
3. Install postgresql-ha: `helm install postgresql-ha .`
   1. To access locally: `kubectl port-forward --namespace default svc/postgresql-ha-pgpool 5432:5432`
   2. If you need to know the password for `postgres` user get the password:
```
kubectl get secret --namespace default db-password \
        -o jsonpath="{.data.postgresql-password}" | base64 --decode
```
4. Install grafana: `helm install grafana .` 
   1. `kubectl port-forward svc/grafana 8080:3000`
   2. Access in browser at: `http://localhost:8080`
   3. Get the `admin` user password using 
```
kubectl get secret grafana-admin --namespace default \
        -o jsonpath="{.data.GF_SECURITY_ADMIN_PASSWORD}" | base64 --decode
```
5. Install webapp: `helm install webapp .`
   1. If you are using ClusterIP
      1. `kubectl port-forward svc/paxful-test-solution-service 8080:80`
      2. Then access in browser: http://localhost:8080
   2. If you are using LoadBalancer then use URL provided by CloudProvider
7. Apply DB migration as described below in migration section
8. Use port-forward from WebApp NOTES to access web and mail ui. How to is also described below

## Secrets
### Database secret
Create database secret `db-password` with following keys:
1. `postgresql-password` - PostgreSQL password
2. `repmgr-password` - PostgreSQL replication manager passwords
```
kubectl create secret generic db-password \
  --from-literal=postgresql-password=PASSWORD \
  --from-literal=repmgr-password=PASSWORD
```
2. Verify secret:
```
kubectl get secrets
```

### App secret
Since webapp was built using Laravel, it requires secret in base64 format.
The create app key secret:
1. Install dependencies with `composer` if not installed:
```
composer install
```
3. Generate key:
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

## Database schema and migration
There is only one table - `user_log` which stores all the blocked IPs.

Database schema is managed with laravel migration. To execute migration do:
1. Copy `.env.example` to `.env` and set/update `DB_*` parameters in `.env` file
2. Install dependencies with composer if didnt: `composer install`
3. Execute
```
php artisan migrate
```

## Local development
### Requirements
1. Docker
2. `composer` - php dependency manager. Download from [here](https://getcomposer.org/) 


### `.env` file and migration
Before starting to develop do the following:
1. Copy `.env.example` to `.env` file and specify DB and mail parameters
2. Start containers with `docker-compose up`
3. Migrate DB schema with `php artisan migrate` manually

### Running integration tests
For the integration you should run test container before. To run them:
1. First run `docker-compose -f docker-compose-test.yml up -d`
2. `php artisan test`

## Build and Push
Docker image is stored in Docker hub.
1. To build: `docker-compose build`
2. To push `docker-compose push`

## Deployment to EKS
### Requirements
* Install `eksctl` [tool](https://eksctl.io/)

### Usage
If you want to deploy and test on EKS you can quickly create cluster using `eksctl` [tool](https://eksctl.io/).
To quickly create/delete cluster you can use `eks.sh` file:
* `./eks.sh create`
* `./eks.sh delete`
