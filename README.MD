Project Name : Merapat [Mesan ruang rapat]
Project Description : Merapat is an application used to easily book meeting rooms.
Project Type : Aplikasi Web
web application development process : 80%

Software used :
- VS Code.
- XAMPP.
- Browser (Chrome and Edge).

Framework and Language used :
- PHP.
- MySQL.
- Bootstrap.
- CSS.
- JavaScript.
- HTML.

Disadvantage points : 
-  Payment logic (Requires API).
-  Notification logic (Identification stage to display website notifications when not opened)
-  Data security is still lacking from SQL injection and cross site scripting (XSS) attacks.

File understanding :
-  Files with the UI naming prefix mean they are used as user displays.
-  Files with view names either at the end or in the middle are used as admin views.
-  Files with names like Users, Bookings, and meeting_admin are used as queries for all actions to be performed.
-  And the file with the action name at the end is used to send and receive actions and send them to the query file.


Next target :
-  Improved display for users and admins.
-  Handle all constraints in unfinished API logic.


Getting to know him briefly:

What is "iisssii"?

Imagine 'iisssii' as a data passport that is required every time you send valuable information to the database gateway. It acts like a meticulous immigration officer, checking and ensuring that each type of incoming data matches the defined category.

If we look at the sequence of characters, 'iisssii' is not just an ordinary arrangement of letters. It's a secret language that tells the database: 'Hey, the data you're about to receive has this order: two integers (i, i), then three strings (s, s, s), and it ends with two more integers (i, i).'

So, when you see $cegah->bind_param("iisssii", $this->user_id, $this->room_id, $this->start_time, $this->end_time, .....);, it means you are instructing the system to treat $this->user_id and $this->room_id as integers, $this->start_time, $this->end_time, and the others...

With this 'iisssii' in place, the database becomes more structured and secure. It knows exactly what type of data is expected in each column, thus preventing potential input errors and security vulnerabilities that might arise if data types are not clearly defined.


Thank you for reading this because it is very important in web application development.
Warm greetings from us developers ☕.