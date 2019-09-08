# swarmon

A simple utility to monitor the number of healthy nodes on a Docker swarm. If the count does not match the expectations, it sends an alert to Slack channel. It is intended to run as a stack service on the swarm. The image is available on hub.docker.com

It is configured via environment variables:

 - SLACK_WEBHOOK: contains the webhook link to the slack channel where problems will be posted
 - NODE_COUNT: the expected number of healthy available nodes
 - CHECK_INTEVAL_MIN: (optional) the number of minutes between checks. Defaults to 5 if not provided. 
