###########################################################################################################################
#                                                                                                                         #
#                                    N96 Localization via Context - MobiLocate                                                         #
#                                                                                                                         #
###########################################################################################################################

Introduction
============
•	Designed a context aware client-server application for Nokia N96 to locate a user in the absence of GPS signals as my final year research project.

•	The client, programmed in Python for S60 and Java ME, collects and transmits the context data wirelessly to the server application and displays the received map of the location.

•	The server consisting of PHP scripts, stores the context data samples in a SQL database, employs a position approximation algorithm with the samples to predict the location and sends it to the client.

•	Utilizes Google Maps API, JSON for data serialisation and third party libraries to collect contextual information such as lighting level, noise level, Wi-Fi signal strength and accelerometer readings.


Client Installation
===================

1) On S60 compatible phone, download the Python S60 SIS package (http://sourceforge.net/projects/pys60/files/pys60/1.4.5/PythonForS60_1_4_5_3rdEd.sis/download).

2) Download the Python Script Shell SIS package (http://sourceforge.net/projects/pys60/files/pys60/1.4.5/PythonScriptShell_1_4_5_3rdEd.SIS/download).

3) Install both the SIS packages on the drive where the scripts are most likely to be installed and executed from. i.e E drive.

4) Download the Wlan tools (http://chris.berger.cx/uploads/PyS60/wlantools/wlantools-PyS60_1_4_5_3rdEdFP2-unsigned.sis).

5) Follow the Signing Instructions to sign the Wlan tools SIS package (http://www.patrickfrei.ch/phonetinfo/dev_app_sign_oso.html).

6) Install the Wlan Tools SIS package on the C drive.

7) Copy Json Module to <DRIVE>/Python/Lib (https://github.com/daran9/n96localization-via-context/blob/master/json.py).

8) Copy the Client folder to <DRIVE>/Client( https://github.com/daran9/n96localization-via-context/blob/master/Client) 

9) Execute <DRIVE>/Client/MobiLocate.py


Server Installation
===================
1) Copy the Server folder to a PHP compatible web-server (https://github.com/daran9/n96localization-via-context/blob/master/Server) 



Moved from Google Code Repos