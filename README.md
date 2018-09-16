# Guestbook

Simple application to accept and store visitors comments.  Uses CouchDB 2.0 for storage and also offers webhooks for realtime updates of new comments via RabbitMQ.

## Deploying to Cloud

Deploy the guestbook from `src/web` directory - `cf push`.

It relies on a CloudantNoSQLDB called "guestbook-db" and a RabbitMQ service called "guestbook-messages"; these services need to be created before you deploy.  In the CloudantNoSQLDB the databases `comments` and `webhooks` also need to be created in advance.

The two workers are also deployable, in the `src/worker` directory, use `cf push commentWorker` and `cf push notificationWorker` respectively (each has their own specific manifest file).

## Instructions for Local Installation and Setup

It's vagrant and ansible.  Type `vagrant up` and your machine will be on `192.168.121.8`.

Start some services:
  - `sudo systemctl start couchdb.service`
  - `sudo systemctl start requestbin.service`
  - `sudo systemctl start guestbook-web.service`
  - `sudo systemctl start guestbook-comment-worker.service`
  - `sudo systemctl start guestbook-notification-worker.service`
  - `sudo systemctl start couchdb-haproxy.service`

You now have a web interface http://192.168.121.8:8080/ and fauxton http://192.168.121.8:5984/_utils/# and rabbit http://192.168.121.8:15672 and requestbin http://192.168.121.8:8001.

Set up two new tables in CouchDB: `comments` and `webhooks`.

CouchDB setup came from here http://couchdb.apache.org/developer-preview/2.0/

## To Show Incoming Webhooks with NGrok

There's a file called `receive2.php` in the webroot of the guestbook frontend, so curl to http://192.168.121.8:8080/receive.php to see an empty array is output.  If you send form data, it dumps `$_POST`, if you send JSON then it decodes that first.

SSH into vagrant and go to `/vagrant`.  There you will find a script that runs the local binary of ngrok with an associated config file that makes the dashboard available on the host machine `run-ngrok-locally.sh`.  Now hit that (optionally from github).

Use `receive3.php` to write the incoming data to CouchDB as a poor-man's queueing option.
