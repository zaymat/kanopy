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
mv src/config.template src/config.json
docker-compose up
```

The website is available at ```localhost:8080```

### On baremetal

/!\ You need a Mysql server on your device /!\

```
git clone https://github.com/zaymat/kanopy /var/www/html
cd /var/www/html
mv src/* .
rm -r src
mv config.template config.json
```
Then modify config.json to fit your database configuration.
(We assume that your apache server serves /var/www/html)

The website is available at ```localhost:8080```

## The project

### Approach
I chose to make the project with PHP only.

I first concentrated myself on the data gathering to have the commits to build the frontend. I chose to use curl to query the github API as it is easy to use and provide all the features I needed. 
Then, I used MariaDB server to created the database to store those commit. I chose to create two tables: one to store the commits (sha, committer, author, date, message) and another to store the authors/committers (name, email, image, github_id).
Then, I started displaying the commits (first view). I chose to display the begining of the commit message, the committer's name, the date of the commit and the hash.
For the second view, I chose to curl the API again to have more information about the commit, especially the file patches.
Thus, I can display author and committer information and file patches with different background color. To do that, I parsed the data with regex to detect additions and deletions.
Finally, I added a bit of CSS to beautify the website and a search bar to check other repositories.

### Libraries
I used only native PHP and Bootstrap.

### Time repartition
I spent approximatly 12h on the project (without README):
* 40% database
* 40% frontend
* 20% features

I spent a lot of time for database integration, due to difficulties to insert data. In fact, PHP PDO doesn't raise errors when data is invalid.

I also spent a lot of time to display data, especially to display patches with color highlighting.

### Methodology improvements
I think a started frontend too early, resulting in a huge loss of time, because the backend wasn't finished yet.

I also spent too much time on database integration because I didn't know very well how to use pdo.

## Possible improvements
 
### Design
The design is really basic and there are some problems with colors and layouts.
With more time, it could be possible to do a way better design. Moreover, I only used HTML/CSS with Bootstrap. With a bit of Javascript it would be more interactive.

### Database
I figure out too late that the database is not really useful in this case. In fact, there are lots of unusual cases that make the database difficult to populate (committers with no account linked, no author or no committer ...). 
As I call the api each time I refresh the page, the database is even more useless.

So there are three ways to improve that. Either deleting the database and do all with local storage, or improving the database schema to treat all the special cases better than today, or updating data at each api call instead of deleting the whole table.

### Github authentication

Today, I curl the API with only a User-Agent header so I am limited to 60 requests per hour.
With a valid github oauth tocken, that limit is 5000 requests per hour.
