# Earth Family

## Pages Bread:
### - Members Page:
* first_name [varchar(255) text - required] bread:
  
        {
            "validation": {
                "rule": "required|string|max:255"
            }
        }
* last_name [varchar(255) text - required] bread:

        {
            "validation": {
                "rule": "required|string|max:255"
            }
        }
* birth_date [date date - required] bread:

        {
            "validation": {
                "rule": "required"
            }
        }
* gender [varchar select - required] bread:
 
        {
            "options": {
                "male": "Male",
                "female": "Female"
            },
            "validation": {
                "rule": "required"
            }
        }
* street [varchar text - nullabe] read:
  
        {
            "validation": {
                "rule": "nullable|string"
            }
        }
* postcode [tinytext text - nullable] bread:

        {
            "validation": {
                "rule": "nullable|string|max:32"
            }
        }
* location [tinytext text - nullable] bread: 
  
        {
            "validation": {
                "rule": "nullable|string|max:255"
            }
        }
* phone [varchar text - nullable] bread:
  
        {
            "validation": {
                "rule": "nullable|string|max:25"
            }
        }
* email [tinytext text - nullable] bread:

        {
            "validation": {
                "rule": "nullable|string|email:rfc,dns|unique:members,email"
            }
        }
* aggreement1 [tinyint checkbox - nullable] read:

        {
            "on": "Agree",
            "off": "Dont Agree",
            "checked": true,
            "description": "I give my consent to the taking of images/photos/video recordings during club events and other activities that I carry out in my capacity as a member of the club. I also agree to the further use of these images for the purpose of informing the public about the association's activities. The images taken can be published by the association. I do not derive any rights (e.g. remuneration) from this consent."
        }
* aggreement2 [tinyint checkbox - nullable] read:
  
        {
            "on": "Agree",
            "off": "Dont Agree",
            "checked": true,
            "description": "I was made aware of the information on data protection and the possibility of downloading the data protection guidelines from the homepage of the Diocesan Conservatory / the Pro Kimukons Association www.kimukons.at/Datenschutz."
        }
* is_verified [tinyint checkbox - required] bread:

        {
            "on": "Verified",
            "off": "Not Verified",
            "checked": true
        }
* validation_code [varchar - text] d:
    
        {}
