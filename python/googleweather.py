import pywapi
import pprint
import string
pp = pprint.PrettyPrinter(indent=4)

print "Google:"
google_result = pywapi.get_weather_from_google('07960')
pp.pprint(google_result)

print "\n\nNOAA:"
noaa_result = pywapi.get_weather_from_noaa('KMMU')
pp.pprint(noaa_result)

print "\n\nGoogle says: It is " + string.lower(google_result['current_conditions']['condition']) + " and " + google_result['current_conditions']['temp_c'] + "C now in Morristown."

print "NOAA says: It is " + string.lower(noaa_result['weather']) + " and " + noaa_result['temp_c'] + "C now in Morristown.\n"

# http://code.google.com/p/python-weather-api/ 
