#
# Sample test messages for a RESTful endpoint. These messages can be executed on the Linux
# command line by calling php-cgi, to quickly test scripts without need for the browser.
# The fidelity to browser results is generally very good UNLESS the script writes out
# information (generally debug info) to the screen that is not consistent with the HTTP
# protocol. So, don't do that.
#
# Test Message 1: updateData
export CONTENT_LENGTH=272;
export BODY='&user=11&pass=somepass&data={"request_type":"1","local_timestamp":"2014-08-06 10:00:00","encounter_id":31, "field":"requested_by", "field_value":"Dr. Dawg"}'
export REDIRECT_STATUS=true
export REQUEST_METHOD=POST;
export SCRIPT_FILENAME=jsonExample.php ;
export CONTENT_TYPE=application/x-www-form-urlencoded;
echo $BODY | php-cgi
# Test Message 2: fetchTodaysCases
export BODY='&user=11&pass=somepass&data={"request_type":"61","location_id":"0","date":"2014-09-29"}'
echo $BODY | php-cgi
# Test Message 3: fetchPdf
export BODY='&user=11&pass=somepass&data={"request_type":"3","encounter_id":"720"}'
echo $BODY | php-cgi
