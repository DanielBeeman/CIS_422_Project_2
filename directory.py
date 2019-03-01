"""
Url and method handler page

"""

import flask
from flask import request
import logging

###
# Globals
###
app = flask.Flask(__name__)


###
# Pages
###


@app.route("/")
@app.route("/index")
def index():
    app.logger.debug("Main page entry")
    return flask.render_template('Home.html')                           #Renders the main page for the website

@app.rout("/admin")
def admin():
    pass

#############

app.debug = True
if app.debug:
    app.logger.setLevel(logging.DEBUG)

if __name__ == "__main__":
    print("Opening for global access on port {}".format(5000))
    app.run(port=5000, host="0.0.0.0")