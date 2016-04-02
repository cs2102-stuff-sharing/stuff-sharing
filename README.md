#CS2102-Stuff-Sharing (Project Group 33)

##Introduction
Stuff Sharing: the system allows people to borrow or lend stuff they own (tools, appliances, furnitures, books) either free or for a fee. Users advertise stuff available (what stuff, where to pick up and return, when it is available, etc.) or can browse the available stuff and bid to borrow it. Stuff owners or the system (your choice) chooses the successful bid.  Administrators can create, modify and delete all entries.

##Prerequisites

1. Install [Sourcetree](https://www.sourcetreeapp.com/)(recommended), or at least Git.
2. Install [Sublime Text](https://www.sublimetext.com/) or any other IDE that can aid in PHP/HTML coding.
3. Install [Bitnami Stack](https://bitnami.com/tag/postgresql) (refer to the CS2102 PHP Handout-binami.pdf for detailed instructions).

##Setting up

1. Create a folder named `stuff-sharing` in the directory: `/apache2/htdocs/`.
2. Pull the code from this repo into the `stuff-sharing` folder.
3. Open up the Bitnami Stack Manager and start both Apache Web Server and Postgres.
4. Open your web server and go to this URL: `localhost:<your-port-number>/stuff-sharing`. Replace `<your-port-number>` with the port number you start your Apache Web Server. You can find that out by clicking `Configure` for the Apache Web Server in your Bitnami Stack Manager.
5. Login to your postgres server with the credentials you entered during installation. Click `SQL` on the top left corner. Copy all the contents from `StuffSharingSchema.sql` and paste it into the window and click `Execute`. This will create the neccessary tables for this website.
5. You can now access this website via this URL: `localhost:<your-port-number>/stuff-sharing`.

##Team Members
- Feng Junhan
- Hoo De Lin
- Lam Weng Cong
- Lin Daqi
- Urvashi Sikka
