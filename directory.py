"""
Url and method handler page
Contributers: Newton Blair (2/25/19), Bryce Di Geronimo (2/25/19)
This will be our main python file that will be responsible for handling URL requests, rendering HTML templates, setting up the localhost environment, 
and sending data to the database.

"""
# import all flask dependencies
import flask
from flask import Flask, flash, request, redirect, url_for, jsonify
import mysql.connector
import logging
from PIL import Image, ExifTags
import io 
import arrow
# import BytesIO



###
# Globals
###
app = Flask(__name__) #central object
app.secret_key = 'secretKey' #need an arbitrary key for flask messages
 
# MySQL configurations
cnx = mysql.connector.connect(port = 3728, host = "ix.cs.uoregon.edu", user = "guest", password = "guest", database = "Parking_Ticket") #develop connection with external database
cursor = cnx.cursor() #create cursor


###
# Pages
###


@app.route("/") #home page
@app.route("/index") #different url for same home page
def index(): #function name
    # app.logger.debug("Main page entry") 
    return flask.render_template('Home.html') #Renders the main page for the website

@app.route('/admin_data', methods=['GET', 'POST']) #main form for admin page, accepts GET and POST Requests
def admin_data():
	image = request.files['upload'] #get raw image
	exif = get_exif(image) #dictionary of string tags and corresponding values
	if('GPSInfo' not in exif):
		flash("Please enter an image with GPS data", 'error')
		return redirect(url_for("admin")) #redirect to admin page
	if exif['Orientation'] != 1:
		image=Image.open(request.files['upload'])
		if exif['Orientation'] == 3:
			image=image.rotate(180)
		elif exif['Orientation'] == 6:
			image=image.rotate(270)
		elif exif['Orientation'] == 8:
			image=image.rotate(90)
		image.show()
		stream = io.BytesIO()
		image.save(stream, "JPEG")
		imagebytes = stream.getvalue()
		image1= imagebytes
	else:
		image1 = image.read()

	app.logger.debug("***************")
	app.logger.debug(exif['Orientation'])
	time = get_dateTime(exif) #get time of when picture was taken
	time1 = time.split(" ")
	first = time1[0].replace(':', '-')
	newstring = first + " " + time1[1]
	arrowObject = arrow.get(newstring)
	final = arrowObject.shift(days=+10)
	dateTime = final.format('YYYY-MM-DD HH:mm:ss')
	app.logger.debug(str(dateTime))
	lat, lon = get_lat_lon(exif) #get lattitude and longitude of picture

	try: #create dictionary of all values received from form with matching entries of database
		ticket_info = {
		'plate' : request.form['plate'],
		'ticketId' : int(request.form['id']),
		'state' : request.form['state'],
		'image' : image1,
		'description' : request.form['description'],
		'lat' : lat,
		'lon': lon,
		'time': time,
		'days': dateTime,
		}

		#create an object with relevant data corresponding to database entries to be sent the the database
		add_ticket = ("INSERT into Tickets " "(idTickets, State, License_Plate, Picture, Latitude, Longitude, Description, Timestamp, End_Date) "
			"VALUES (%(ticketId)s, %(state)s, %(plate)s, %(image)s, %(lat)s, %(lon)s, %(description)s, %(time)s, %(days)s) ") 

		cursor.execute(add_ticket, ticket_info) #commit data to database
		cnx.commit() #commit connection
		flash("Successful database insertion", 'color')
		return redirect(url_for("admin")) #redirect to admin page

	except mysql.connector.Error as err: #if sending to database doesn't work
		app.logger.debug(err)
		flash("Error: Could not successfully insert data", 'error') #add error to flash messages to be displayed if submission to database doesnt work
		flash("Please verify that the ID is unique", 'error') #add error to flash messages to be displayed if submission to database doesnt work
		return redirect(url_for("admin")) #redirect to admin page

	# cursor.close()
	# cnx.close()
	

@app.route("/admin")
def admin():
	return flask.render_template('Admin.html') # render admin page

'''
Convert image into EXIF data, then grab lat+lng

There's a datetime included = 'DateTime' and 'DateTimeOriginal'
This might help tell the difference: https://mail.gnome.org/archives/f-spot-list/2005-August/msg00081.html

Getting tags based on https://stackoverflow.com/questions/4764932/in-python-how-do-i-read-the-exif-data-for-an-image

get_lat_lon, convert_to_degrees, get_float directly from https://gist.github.com/erans/983821/e30bd051e1b1ae3cb07650f24184aa15c0037ce8

'''


def get_exif(image):
	''' Returns dictionary of string tags and corresponding values '''
	img = Image.open(image)
	exif = {
	    ExifTags.TAGS[k]: v
	    for k, v in img._getexif().items()
	    if k in ExifTags.TAGS
	}
	return exif

get_float = lambda x: float(x[0]) / float(x[1]) 

#convert lat and long format to degrees
def convert_to_degrees(value):
    d = get_float(value[0])
    m = get_float(value[1])
    s = get_float(value[2])
    return d + (m / 60.0) + (s / 3600.0)

#get lattitude and longitude from image
def get_lat_lon(exif):
	try:
		info = exif["GPSInfo"]
		gps_latitude = info[2]
		gps_latitude_ref = info[1] # N or S
		gps_longitude = info[4] # 3 tuples
		gps_longitude_ref = info[3]

		lat = convert_to_degrees(gps_latitude)
		if gps_latitude_ref != "N":
			lat *= -1

		lon = convert_to_degrees(gps_longitude)
		if gps_longitude_ref != "E":
			lon *= -1
		return lat,lon 
       

	except KeyError:
		return None

#gets the data and the time of when the picture was taken
def get_dateTime(exif):
	return exif["DateTime"]


#############

app.debug = True #allow us to see changes live on our environment
if app.debug:
    app.logger.setLevel(logging.DEBUG)

if __name__ == "__main__":
    print("Opening for global access on port {}".format(5000)) #let the user know which port the application is being run on
    app.run(port=5000, host="0.0.0.0") #set the port = 5000 and run the application on local host