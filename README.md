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

Update your messenger app's event subscription setting to point to the ngrok url above. Validate with the token you provide, e.g. `masterbruce`. Once validated, you are now ready to interact with the bot.

> If you stop ngrok and run it again, it will give you another ngrok endpoint when you use the free plan. This means that you need to update your messenger apps' url setting to point to the new ngrok endpoint.

# Contributing Guide

First off, thank you for taking the time to contribute!

When contributing to this repository, please first discuss the change you wish to make via issue, email, or any other method with the owners of this repository before making a change.

Local development workflow:

```
- Postman sends request -> FbBot receives requests -> Commands process messages -> Sends back response -> Postman displays response
                                FbBot.php               \Commands\Twitter.php
                                                        \Commands\Reminder.php
                                                        \Commands\Base.php
```

---

Code management:

### Making updates

- Create a branch. Branch out from master always. `-b` means create and checkout a new branch. For more help, type `git {command} -h`
```
git checkout master
git checkout -b feature/simple-reminder
```
- Make changes.
- Add changes.
```
git add {list of files you want to add}
```
- Commit to changes made. Describe what the update is about.
```
git commit -m "Add a simple reminder list"
```
- Make more changes and follow the previous steps. Or, when you think you have to save this to our remote repository (e.g. aldnav/alfred-chatbot)
`git push origin feature/simple-reminder`

### Pulling updates
- Pull updates from the branches you need
```
git checkout feature/simple-reminder
```
- If there are conflicts, check the files listed. Resolve them first by editing each conflicted file.

### Creating a Pull Request
When you are ready to get your code reviewed, create a pull request.
"Base" means the branch where you want it merged. So for example, you are done with the `simple-reminder` feature, you create a `base: master <- compare:feature/simple-reminder` pull request.

Wait for others to review your work. This is important to keep the `master` branch stable and working.
