#***********************************************************************************
#***********************************************************************************
# EECE496  Localization via Context Python application for Nokia N96
# MobiLocate
# Sun Kumaragurubaran
# Technical Supervisor: Dr.Satish Gopalakrishnan
#***********************************************************************************
#***********************************************************************************

#-----------------------------------------------------------------------------------
# Imports
#-----------------------------------------------------------------------------------
import appuifw, e32, positioning, graphics, os
import socket, urllib, httplib, wlantools, json

#-----------------------------------------------------------------------------------
# Variables
#-----------------------------------------------------------------------------------
URL_IMAGE_UPLOAD = "http://daran.homelinux.net/~sun/mobilocate/image_map.php"
DEST_FILE = u"E:\\Images\\imageMap.png"
URL_LOC_DATA = "/~sun/mobilocate/loc_data.php"
URL_SETUP_DATA = "/~sun/mobilocate/setup_data.php"
URL_UPDATE_DATA = "/~sun/mobilocate/update_data.php"
HOSTNAME = "daran.homelinux.net"
HEADERS = {"Content-type": "application/x-www-form-urlencoded","Accept": "text/plain"}
STATUS_MSG = u"Welcome to MobiLocate!"
APP_TITLE = u"MobiLocate"
PHOTO_FILE = u"E:\\Images\\photo_upload.jpg"
URL_PHOTO_UPLOAD = "/~sun/mobilocate/update_photo.php"
SSID_SCAN = 'ubc'

#Make a list of objects,
BLDG_LIST = [u"KAIS", u"MCLD"]
FLOOR_LIST = [u"1", u"2", u"3", u"4"]
SECTION_LIST = [u"A", u"B", u"C", u"D", u"E", u"F"]

#Create the fields to be displayed in the form
FORM_FIELDS = [(u"Building", 'combo', (BLDG_LIST, 0)),
			(u"Floor", 'combo', (FLOOR_LIST, 0)),
			(u"Section", 'combo', (SECTION_LIST, 0))]

#-----------------------------------------------------------------------------------
# Functions
#-----------------------------------------------------------------------------------

# Summary : This function scans the list of WiFi access points and returns the list in unicode strings 
# containing the MAC address(BSSID) and the corresponding signal strength(RSSI) to be displayed 
# Parameters : None
# Return : List of BSSID and RSSI
def scanWifiDisp():
	netList = []
	scanInfo = wlantools.scan(False)
	#loop over all the available WiFi Networks
	for scanNet in scanInfo:
		sigLevel = scanNet['RxLevel']
		sigSSID = scanNet['SSID']
		sigBSSID = scanNet['BSSID']
		if sigSSID == SSID_SCAN :
			netList.append((str(sigBSSID) + u" ", str(sigLevel) + u" "))
	return netList

# Summary : This function scans the list of WiFi access points and return the list containing the 
# MAC address(BSSID) and the corresponding signal strength(RSSI)
# Parameters : None
# Return : List of BSSID and RSSI
def scanWifi():
	netDict = []
	scanInfo = wlantools.scan(False)
	#loop over all the available WiFi Networks
	for scanNet in scanInfo:
		sigLevel = scanNet['RxLevel']
		sigSSID = scanNet['SSID']
		sigBSSID = scanNet['BSSID']
		if sigSSID == SSID_SCAN :
			netDict.append({'BSSID': str(sigBSSID), 'RSSI' : sigLevel})
	return netDict


# Summary : This function gets the last known GPS location
# Only availabe in PyS60 2.0
# Parameters : None
# Return : List of altitude, latitude, longitude
def getLastPosition():
	#Only support in PyS60 2.0
	#For altitude : gpsPosition['altitude']
	#For latitude : gpsPosition['latitude']
	#For longitue : gpsPosition['longitude']
	try:
		gpsPosition = positioning.last_position()
	except:
		appuifw.note(u"Permission Denied", "error")
	return gpsPosition


# Summary : This function takes a photo, saves as JPEG file and uploads it to the server 
# Parameters : None
# Return : None
def photoUpload():
	photo = camera.take_photo()
	photo.save(PHOTO_FILE)
	image = file(PHOTO_FILE).read()
	conn = httplib.HTTPConnection(HOSTNAME)
	conn.request("POST", URL_PHOTO_UPLOAD, image)
	conn.close()

# Summary : This function gets Get Accelerometer data using the Sensor FW
# Only availabe in PyS60 2.0
# Parameters : None
# Return : None
def accelSensor():
	#Get the acceleration sensor
	#accsensor=sensor.get('AccSensor')
	#id=accsensor.get('id')
	#cat=accsensor.get('cat')
	#senobj=sensor.Sensor(id,cat)
	#senobj.set_event_filter(sensor.OrientationEventFilter())
	pass
	
	
# Summary : This function sets the default access point
# Parameters : None
# Return : None
def defaultAP():
	ap_id = socket.select_access_point()
	apo = socket.access_point(ap_id)
	socket.set_default_access_point(apo)


# Summary : This function gets the list of WiFi data, sends to the server and retrieves the location information 
# as well as sends the GPS data and retrieves the image map file
# Parameters : None
# Return : None
def getLocData():
	global dispResult
	#Get the list of WiFi scan data
	netDict = scanWifi()
	params = urllib.urlencode({'WLANDATA': json.write(netDict)})
	#Connects to the HTTP Server and sends the POST data
	try:
		conn = httplib.HTTPConnection(HOSTNAME)
		conn.request("POST", URL_LOC_DATA , params, HEADERS)
		resp = conn.getresponse()
		locResult = resp.read()
		#Parse JSON result to retrieve location information
		dispResult = json.read(locResult)
		print dispResult
		conn.close()
	except:
		appuifw.note(u'Problem connecting with server!','error')
	
	#Retrive Image map with gps location
	#lat = gpsPosition['latitude']
	#long = gpsPosition['longitude']
	lat=49.2614948
	long=-123.2499465
	urlImage = URL_IMAGE_UPLOAD + "?lat=" + str(lat) +"&long=" + str(long)
	try:
		urllib.urlretrieve(urlImage, DEST_FILE)
	except:
		appuifw.note(u'Problem connecting with server!','error')
	appuifw.note(u"Location Retrived!", 'info')
	
	#Display Map and location information
	appuifw.app.activate_tab(0)
	dispLocData()
	dispImageMap()

#-----------------------------------------------------------------------------------
# Application Tabs
#-----------------------------------------------------------------------------------

# Summary : This Application tab displays the image map file if availabe or
# displays the welcome text when first invoked.
# Parameters : statusArg
# Return : None
def dispImageMap(statusArg = None):
	global canvas, img, status
	img = None
	status = statusArg
	if os.path.exists(DEST_FILE):
		img = graphics.Image.open(DEST_FILE)
	canvas = appuifw.Canvas(event_callback=None, redraw_callback=handle_redraw)
	appuifw.app.body = canvas

# Summary : This Application tab diplays the list of scan data
# Parameters : None
# Return : None
def scanData():
	netList = scanWifiDisp()
	#Create an instance of Listbox and set it as the application's body
	lb = appuifw.Listbox(netList, lambda:None)
	appuifw.app.body = lb
	
#-----------------------------------------------------------------------------------
# Application Forms
#-----------------------------------------------------------------------------------

# Summary : This Application form displays the location informtion
# Parameters : None
# Return : None
def dispLocData():
	disp = {'BLDG': " ", 'FLOOR': " ",'SECTION': " "}
	if dispResult:
		disp = dispResult
	#Create the fields to be displayed in the form
	locFields = [(u"Building", 'text', disp['BLDG'].decode("utf-8")),
				(u"Floor", 'text', str(disp['FLOOR']).decode("utf-8")),
				(u"Section", 'text', disp['SECTION'].decode("utf-8"))]
	#Create an instance of Form
	locForm = appuifw.Form(locFields, flags=appuifw.FFormViewModeOnly)
	#Execute the form
	locForm.execute()


# Summary : This Application form is used for inserting setupdata in database
# Parameters : None
# Return : None
def setupData():
	#Create an instance of Form
	setupForm = appuifw.Form(FORM_FIELDS, flags=appuifw.FFormEditModeOnly)
	#Assign the save function
	setupForm.save_hook = save_setup_form
	#Execute the form
	setupForm.execute()


# Summary : This Application form is for updating data in database
# Parameters : None
# Return : None
def updateData():
	#Create an instance of Form
	updateForm = appuifw.Form(FORM_FIELDS, flags=appuifw.FFormEditModeOnly)
	#Assign the save function
	updateForm.save_hook = save_update_form
	#Execute the form
	updateForm.execute()
	
#-----------------------------------------------------------------------------------
# Handlers
#-----------------------------------------------------------------------------------

# Summary : This handler handles quit event
# Parameters : None
# Return : None
def quit():
	print "Exit Key pressed!"
	os.remove(DEST_FILE)
	app_lock.signal()

	
# Summary : This handler switches between tabs
# Parameters : None
# Return : None
def handle_tab(index):
	#Switch to the tab according to index
	if(index==0):
		# Image Map Application Tab
		dispImageMap()
	if(index==1):
		# Scan Data Application Tab
		scanData()

#
# Summary : This handler redraws the canvas
# Parameters : None
# Return : None
def handle_redraw(rect):
		if img:
			canvas.blit(img)
		else:
			canvas.clear((255,255,255))
		if status:
			canvas.text((10,50), status, fill=(0,0,255), font="title")
		

# Summary : This handler is called when the Setup form is saved. It gets the FORM_FIELDS and WiFi Scan data
# and sends the information to the server script to be added to the database.
# Parameters : FORM_FIELDS
# Return : None
def save_setup_form(arg):
	#global bldg, floor, section
	for label, type, value in arg:
		if label == "Building":
			bldg = value[0][value[1]]
		elif label == "Floor":
			floor = value[0][value[1]]
		elif label == "Section":
			section = value[0][value[1]]
		#print value[0][value[1]]
	netDict = scanWifi()
	#The wlan data is encoded in JSON notation and send as POST parameters
	# together with building, floor and section information.	
	params = urllib.urlencode({'WLANDATA': json.write(netDict), \
							'BLDG':bldg, 'FLOOR':floor, 'SECTION':section})
	#Connects to the HTTP Server and sends the POST data
	try:
		conn = httplib.HTTPConnection(HOSTNAME)
		conn.request("POST", URL_SETUP_DATA , params, HEADERS)
		conn.close()
	except:
		appuifw.note(u'Problem connecting with server!','error')
	appuifw.note(u"Location Data Sent!", 'info')
	
# Summary : This handler function is called when the Update form is saved. It gets the FORM_FIELDS and WiFi Scan data
# and sends the information to the server script to be updated in the database.
# Parameters : FORM_FIELDS
# Return : None
def save_update_form(arg):
	#global bldg, floor, section
	for label, type, value in arg:
		if label == "Building":
			bldg = value[0][value[1]]
		elif label == "Floor":
			floor = value[0][value[1]]
		elif label == "Section":
			section = value[0][value[1]]
		#print value[0][value[1]]
	netDict = scanWifi()
	#The wlan data is encoded in JSON notation and send as POST parameters
	# together with building, floor and section information.	
	params = urllib.urlencode({'WLANDATA': json.write(netDict), \
							'BLDG':bldg, 'FLOOR':floor, 'SECTION':section})
	#Connects to the HTTP Server and sends the POST data
	try:
		conn = httplib.HTTPConnection(HOSTNAME)
		conn.request("POST", URL_UPDATE_DATA , params, HEADERS)
		conn.close()
	except:
		appuifw.note(u'Problem connecting with server!','error')
	appuifw.note(u"Location Data Updated!", 'info')
		
#-----------------------------------------------------------------------------------
# Application
#-----------------------------------------------------------------------------------
 				
appuifw.app.exit_key_handler = quit
appuifw.app.title = APP_TITLE
#Define the menu items and their callback functions 
appuifw.app.menu = [(u"Set Access Point", defaultAP),(u"Get Location",getLocData),(u"Show Location", dispLocData),(u"Setup", setupData),(u"Update", updateData),(u"Exit", quit)]


#Set the tabs
appuifw.app.set_tabs([u"Map", u"Sensor Data"], handle_tab)
#Start Application and display Welcome message
dispImageMap(STATUS_MSG)

#Create an active object
app_lock = e32.Ao_lock()
#Wait for the user to request the exit
app_lock.wait()