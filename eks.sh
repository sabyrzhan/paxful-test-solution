#!/usr/bin/env bash
action=$1
cluster_name=paxful-eks
case $action in
  create)
    eksctl create cluster \
        --name $cluster_name \
        --region us-east-2 \
        --nodegroup-name mainGroup \
        --node-type t3.medium \
        --nodes 3
    ;;
  delete)
    eksctl delete cluster --name $cluster_name --region us-east-2
    ;;
  *)
    echo 'Action should be one of: create or delete';
    exit 1;
    ;;
esac