'''
Convert image into EXIF data, then grab lat+lng

There's a datetime included = 'DateTime' and 'DateTimeOriginal'
This might help tell the difference: https://mail.gnome.org/archives/f-spot-list/2005-August/msg00081.html

Getting tags based on https://stackoverflow.com/questions/4764932/in-python-how-do-i-read-the-exif-data-for-an-image

get_lat_lon, convert_to_degrees, get_float directly from https://gist.github.com/erans/983821/e30bd051e1b1ae3cb07650f24184aa15c0037ce8

'''
from PIL import Image, ExifTags
from subprocess import Popen

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

        gps_latitude = info[2] # 3 tuples
        gps_latitude_ref = info[1] # N or S
        gps_longitude = info[4] # 3 tuples
        gps_longitude_ref = info[3] # E or W
        
        lat = convert_to_degrees(gps_latitude)
        if gps_latitude_ref != "N":
            lat *= -1

        lon = convert_to_degrees(gps_longitude)
        if gps_longitude_ref != "E":
            lon *= -1
        
        return lat, lon
    except KeyError:
        return None

def get_dateTime(exif):
	return exif["DateTime"]

def main():
	# 'IMG_2376.JPG'
	exif = get_exif('water-22-png.png')
	#print(exif)
	lat, lon = get_lat_lon(exif)
	dateTime = get_dateTime(exif)
	#print("lat: " + str(lat) + " lon: " + str(lon))
	#print(get_dateTime(exif))
	p = Popen(['/usr/bin/php','<php file name>',dateTime],stdout=PIPE)
	print p.stdout.read()

main()