# CIS_422_Project_2
The repository for students in group 6 taking CIS 422, Software Methodologies. 
## Description
This system is made for people who give parking tickets, to collect ticket data and present it to people who receive tickets. Those who receive tickets can view a picture of the offense on a Google Map at the spot at which they were ticketed. This is to make it easier for both parties to handle contesting tickets.
## User Documentation
This document outlines a step-by-step process for getting our application up and running on your local machine with an Internet connection.

##Initial Setup

###Forking Repository

1. Please visit our Github at https://github.com/DanielBeeman/CIS_422_Project_2/. Once you have either signed in or created a Git account, please fork our repository by clicking the Fork button in the top right corner. 

2. Once you have forked this repository, please go to it by clicking on the arrow next to your icon at the very top right of the page. A drop down menu should appear and please select “Your repositories.” Click the CIS_422_Project_2 repository. 

3. Now click the green Clone or download button. A drop down will appear with an https link. You may manually copy and paste the link or click the clipboard icon next to the link which will copy it for you.
FOR MAC USERS:
4. Please open a terminal by hitting “command + space” and type in “terminal” or you may use the search icon in the top right corner of your screen and type in “terminal”. Whichever way you choose, press enter after. This will open up a terminal window.

5. Change directory into your Desktop by typing the command: cd Desktop

6. Now type in the command: git clone yourcopiedlinkhere 
An example would be 
git clone https://github.com/brycedigeronimo/CIS_422_Project_2.git
You now have a copy of the repository on your Desktop.




###Documentation for installing the database and setting up IX
* Ensure you are connected to internet.
* Open the application titled ‘Terminal’.
* Connect to your ix account using your login credentials. This can be done using the following command: “ssh example@ix.cs.uoregon.edu”, where ‘example’ is your ix username. 
* You may be prompted to enter your password.
* If you have not installed MySQL on ix, follow instructions 6 and 7. If you already have, continue at step 8. Credit to “https://classes.cs.uoregon.edu/18F/cis451/links.html” for instructions on installing MySQL on ix.
* Type the following: “mysqlctl install”.
* If prompted, enter a database password that will be used to run certain MySQL commands.
* Enter in the following command to start MySQL: “mysqlctl start”.
* Use vim to edit your .my.cnf file by typing the following command: “vim .my.cnf”.  **Do not forget about the “.” before “my” when running the above command**                                                   **If you cannot see the file, enter the following command to find hidden files: “ls -a”**
* In “.my.cnf” there is a line that needs to be commented out to work properly. To do this, find the line that reads “skip-innodb”, and press ‘i’ on the keyboard to begin editing the file. Go to the beginning of the line, and type the “#” key, and the line is now commented out. 
* Additionally, find the line that reads “default-storage-engine=myisam”, and comment this line in the same way you commented in part 10 of this guide. 
* To save your changes, press the “esc” or escape key, then type “:wq” to write your changes to the file, and then quit out of the file. 
* Now type in the following command: “mysqlctl stop”, and then “mysqlctl start” to restart MySQL and update the database with your changes. 
* To run MySQL from the command line, enter the following command: “mysql -p”, or if that does not work, try “mysql -h ix -p”.
* Next, enter in the following command: “CREATE USER ‘example’@’%’ IDENTIFIED BY ‘password’;” where ‘example’ is whatever you would like your username to be, and ‘password’ is whatever password you would like to use. Both the username and password will be used when connecting to the database.                                                                                    **For this step and the next, do not forget the single quotation marks and semicolons in the commands**
* Next, enter the following command: “GRANT ALL PRIVILEGES ON *.* TO ‘example’@’%’ WITH GRANT OPTION;” where ‘example’ is your newly created username.
* In the following steps, we will create the database and set up a table with appropriate columns.
* Next, we will create the database! Yay! Type in the following command: “CREATE DATABASE example;”
* To use the database, type in the following command: “USE example;”.
* Next, use this command to create a table with the correct columns. “CREATE TABLE Tickets (idTickets INT(11), State  VARCHAR(45), License_Plate VARCHAR(7), Picture LONGBLOB, Latitude DECIMAL(10,7), Longitude DECIMAL(10,7), Description VARCHAR(1000), Timestamp DATETIME, End_Date DATETIME);”
* Now, use this command to find your port: SHOW GLOBAL VARIABLES LIKE 'PORT'; Please remember the port number.
* To exit MySQL, type in the following command: “quit”.
* The next step is to set up your virtual environment.
* Please log out of your ix account first.


###Setting up the Virtual Environment


1. While in the terminal, check the version of python that you are running by typing in the command: python3 --version
If your version of python does not equal 3.7.2, you will need to download the most current version of Python

Check the version of your operating system by clicking the apple icon in the top left corner of your screen. Then select the first option, “About This Mac”. Know that you know your current version of your operating system, please go to https://www.python.org/downloads/release/python-372/ and click the downloads button. However, first check your that your macOS is 10.9 or higher.
If your version is less than 10.9, please select the macOS 64-bit/32-bit installer option(the first one). If your operating system version is greater than or equal to 10.9, please select the macOS 64-bit installer. Please click on the download when it comes up in your browser and follow the instructions. 

2. Once Python version 3.7.2 has been installed, please go back to your terminal window.

3. Please cd into your Desktop if you are not currently there. You can see which directory you are in by typing in the command: pwd 

4. Now that you are in your Desktop, you will want to change into forked repository directory. To do so, type the command: cd CIS_422_Project_2
5. Next, we are going to edit the directory.py page in the virtual environment in order to allow you to upload ticket information. Open up “directory.py” by typing in the following command: “vim directory.py”. 

6. Go down to the line that has the connection data. It is likely line 27.

7. Change the port to be your port number from MySQL. Change the user to be your MySQL username. Change the password to be your MySQL password. Change the database to be your database name. Make sure you keep the quotation marks that surround the port, host, user, password, and database names.


8. Now that you’re in that directory, you need to create virtual environment.

9. For a link to the instructions, you can follow http://flask.pocoo.org/docs/1.0/installation/ for mac users or follow the same instructions here:
Python3.7 -m venv venv
. venv/bin/activate
10. Now your virtual environment is set up. To install all the requirements, please run the command:  pip3 install -r requirements.txt

11. You are now ready to test out the system. 
###Running The Application

1. With the virtual environment running, to run the program, please type the command: 
python3 directory.py

2. To submit ticket violations for administrators, please open a browser such as Firefox, Safari, or Google Chrome and go to http://0.0.0.0:5000/admin











* Now, in order to view parking tickets in your database, you will need to edit the “Ticket_db.php” file. 
Update the username, password, database, and port near the top of the file to match your username, password, database and port number.
* Now send the Ticket_db.php and Home.html file to your public_html folder on ix. Do this with the following command: “scp Home.html Ticket_db.php yourixaccount@ix.cs.uoregon.edu:public_html”.
* Write to the file and save your changes by typing “:wq”, and test that you can draw from the database by going to the following link on a web browser: “http://ix.cs.uoregon.edu/~example/Home.html”.  **if you placed this in a subdirectory on ix, you will need to include that file path to the URL**
* Enter the License plate you entered into your database, along with the appropriate state from the drop-down menu. 
* Next, select “View Tickets” to see if your new entry appears on the page and map. If so, you are done setting up the system! 


###Continuous Use
Once the setup is finished, you can just go to http://0.0.0.0:5000/admin to submit tickets and http://ix.cs.uoregon.edu/~example/Home.html where example is your IX username to view your tickets.

