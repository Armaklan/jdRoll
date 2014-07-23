jdRoll
======

Opensource platform of Roleplaying Game by Forum. Use to build [this site](http://jdroll.org).
MIT License - Use it (the site, or the source) and enjoy it !

Contribute using Vagrant
------------------------

### Requirment

Vagrant must be installed and correctly configure

### Instruction

 - Run ```vagrant up``` (Initialize VM and do provisionning)
 - Access to http://localhost:8080


Contribute using Manual Installation
------------------------------------

### Requirment

- PHP 5.4 with Openssl module
- Mysql database
- Node

In Windows Environment, we recommend to use Git Bash to launch sh script.

### Instruction

- Clone this repository
- Launch ```bin/bootstrap.sh``` (Download composer, install php and web dependencies, initialize database)
- Launch ```grunt dev```
- Access to http://localhost:8000