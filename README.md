This is an example of a simple RESTful endpoint: some PHP catcher code for JSON from a mobile app. 

The interesting part is probably in the MySQL schema. By storing the indvididual piece of data 
in table json_data,along with a pointer to the json_property table that explains what the data 
represents, this creates a tall skinny table that expands to cover many different types. Two 
basic objects, with their own tables, might be patients and encounters. An encounter is 
associated with a patient, and the entries in the json_data table are associated with an encounter 
and a property. The property tells what the data is, and the encounter tells who it's associated with.

The json_data table works great for simple pieces of data, but you need a few more fields to manage 
data that comes in pairs or tuples. Examples of this would be vitals data, which is associated with
a property and a time, or drug data, which includes the drug type, time, and dosage.

Another fun aspect of this schema is the case/encounter hierarchy. A case can have multiple parts,
which often correspond to separate paper forms. A surgery might have forms for preop, intraop, 
postop, patient satisfaction, and more. Surgeries at different locations might have different 
forms, or different types of encounters for the same type of case. Storing this on the server 
makes the mobile client nimble and easily customizable.
