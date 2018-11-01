🤵 🤖 Alfred
---

Alfred's proxy chatbot written in PHP

Entry point: `bot.php`

# Setup instructions
> LAMP/XAMPP stack must be setup first

```sh
cd public_html/  # or /var/www/html/
git clone git@github.com:aldnav/alfred-chatbot chatbot
cd chatbot
composer install
ngrok http
# should now be accessible via
# if from public_html/ - https://<ngrok-url>/~<pc-username>/chatbot/bot.php - e.g. https://g2kcx3.cf/~aldnav/chatbot/bot.php
# if from /var/www...  - https://<ngrok-url>/chatbot/bot.php
# visit web portal provided by running the ngrok command above
```

Update your messenger app's event subscription setting to point to the ngrok url above. Validate with the token you pro vide, e.g. `masterbruce`. Once validated, you are now ready to interact with the bot.

> If you stop ngrok and run it again, it will give you another ngrok endpoint when you use the free plan. This means that you need to update your messenger apps' url setting to point to the new ngrok endpoint.

# Contributing Guide

First off, thank you for taking the time to contribute!

When contributing to this repository, please first discuss the change you wish to make via issue, email, or any other method with the owners of this repository before making a change.

Please note we have a code of conduct, please follow it in all your interactions with the project.

## Pull Request Process

1. Ensure any install or build dependencies are removed before the end of the layer when doing a 
   build.
2. Update the README.md with details of changes to the interface, this includes new environment 
   variables, exposed ports, useful file locations and container parameters.
3. Increase the version numbers in any examples files and the README.md to the new version that this
   Pull Request would represent. The versioning scheme we use is [SemVer](http://semver.org/).
4. You may merge the Pull Request in once you have the sign-off of two other developers, or if you 
   do not have permission to do that, you may request the second reviewer to merge it for you.
