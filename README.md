# kanopy
Technical test for Kanopy recruitment process

## Requirements

This project requires :
* PHP v7 (for baremetal only)
* pdo and pdo_mysql extensions (for baremetal only)
* Apache v2 (for baremetal only)
* docker-compose (for docker only)

## Installation

### With Docker

```
mv config.template config.json
docker-compose up
```

The website is available at ```localhost:8080```

### On baremetal

/!\ You need a Mysql server on your device /!\

```
git clone https://github.com/zaymat/kanopy /var/www/html
cd /var/www/html
mv config.template config.json
```
(We assume that your apache server serves /var/www/html)

The website is available at ```localhost:8080```

## Approach of the subject



## Possible improvements
 
### Design
The design is really basic and there are some problems with colors and layouts.
With more time, it could be possible to do a way better design. Moreover, I only used HTML/CSS with Bootstrap. With a bit of Javascript it would be more interactive.

### Database
I figure out too late that the database is not really useful in this case. In fact, there are lots of unusual cases that make the database difficult to populate (committers with no account linked, no author or no committer ...). 
As I call the api each time I refresh the page, the database is even more useless.

So there are three ways to improve that. Either deleting the database and do all with local storage, or improving the database schema to treat all the special cases better than today, or updating data at each api call instead of deleting the whole table.
