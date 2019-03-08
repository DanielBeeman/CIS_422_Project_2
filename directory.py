"""
Url and method handler page

"""

import flask
from flask import Flask, request, redirect, url_for
import mysql.connector
# import dataExtraction
import logging
from PIL import Image, ExifTags
from io import BytesIO


###
# Globals
###
app = Flask(__name__)

 
# MySQL configurations
cnx = mysql.connector.connect(port = 3728, host = "ix.cs.uoregon.edu", user = "guest", password = "guest", database = "Parking_Ticket")
cursor = cnx.cursor()


###
# Pages
###


@app.route("/")
@app.route("/index")
def index():
    app.logger.debug("Main page entry")
    return flask.render_template('Home.html')                           #Renders the main page for the website

@app.route('/admin_data', methods=['GET', 'POST'])
def admin_data():
	image = request.files['upload']
	image1 = request.files['upload'].read()
	exif = get_exif(image)
	lat, lon = get_lat_lon(exif)
	ticket_info = {
		'plate' : request.form['plate'],
		'ticketId' : request.form['id'],
		'state' : request.form['state'],
		'image' : image1,
		'description' : request.form['description'],
		'lat' : lat,
		'lon': lon,
		'days': 1,

	}
	# ticket_info = {
	# 	'plate' : 1,
	# 	'ticketId' : 1,
	# 	'state' : 'ca',
	# 	'image' : 'asdf',
	# 	'description' : 'asdf',
	# 	'lat' : 1,
	# 	'lon': 1,
	# 	'days': 1,

	# }
	

	# insert into db

	# "INSERT into Tickets (idTickets, State, License_Plate, Picture, Latitude, Longitude, Timestamp, Days_Left, Description) 
	# VALUES ('$id', '$state', '$plate', '$image', NULL, NULL, NULL, NULL, '$descr')"


	add_ticket = ("INSERT into Tickets " "(idTickets, State, License_Plate, Picture, Latitude, Longitude, Days_Left, Description) "
		"VALUES (%(ticketId)s, %(state)s, %(plate)s, %(image)s, %(lat)s, %(lon)s, %(days)s, %(description)s) ")
		# "VALUES (%(ticketId)s, %(state)s, %(plate)s, %(image)s, %(lat)s, %(lon)s, %(time)s, %(days)s, %(description)s) ")
		# "VALUES ('$id', '$state', '$plate', '$image', NULL, NULL, NULL, NULL, '$descr') ")

	cursor.execute(add_ticket, ticket_info)
	cnx.commit()

	# cursor.close()
	# cnx.close()






	# app.logger.debug("lat: " + str(lat) + " lon: " + str(lon))
	# app.logger.debug(get_dateTime(exif))


	# app.logger.debug(ticketId)
	# app.logger.debug(state)
	return redirect(url_for("admin"))

@app.route("/admin")
def admin():
	return flask.render_template('Admin.html')

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

def convert_to_degrees(value):
    d = get_float(value[0])
    m = get_float(value[1])
    s = get_float(value[2])
    return d + (m / 60.0) + (s / 3600.0)


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


def get_dateTime(exif):
	return exif["DateTime"]

# def main():
# 	exif = get_exif('IMG_2376.JPG')
# 	#print(exif)
# 	lat, lon = get_lat_lon(exif)
# 	print("lat: " + str(lat) + " lon: " + str(lon))
# 	print(get_dateTime(exif))

# main()



#############

app.debug = True
if app.debug:
    app.logger.setLevel(logging.DEBUG)

if __name__ == "__main__":
    print("Opening for global access on port {}".format(5000))
    app.run(port=5000, host="0.0.0.0")