ST. LEO'S COLLEGE KYEGOBE - WEBSITE (plain PHP, no database needed)
====================================================================

ADMIN / MANAGEMENT SYSTEM LOGIN
  Link   : footer of every page -> "Staff & Management Login"
           (or go directly to  /admin/login.php)
  Username: admin
  Password: StLeo@2026
  >> Change the password after first login: Dashboard -> Settings & Password.

DASHBOARD SECTIONS
  1. Dashboard        - overview and counters
  2. Home Slides      - edit the 3 homepage slides (picture, heading, text, button)
  3. News & Updates   - add / edit / delete articles (Academics & UNEB, Sports, School Life)
  4. Gallery          - upload photos, edit captions & categories, hide or delete
  5. Messages         - inbox for the Contact Us form
  6. Settings & Password - contact details, mission, vision, history, map text, password

RUN ON WAMPSERVER
  1. Unzip the folder "stleos-kyegobe" into  C:\wamp64\www\
  2. Start WampServer (icon must be green).
  3. Open  http://localhost/stleos-kyegobe/
  4. Admin: http://localhost/stleos-kyegobe/admin/login.php
  (Needs PHP 7.4 or newer. No MySQL setup is required.)

HOST ON INFINITYFREE
  1. Log in to the InfinityFree control panel -> Online File Manager (or use FTP).
  2. Open the folder  htdocs/
  3. Upload everything INSIDE "stleos-kyegobe" (index.php, about.php, admin, assets,
     data, includes ...) straight into htdocs/  (so index.php sits in htdocs/index.php).
  4. Visit your domain. Admin: yourdomain/admin/login.php
  5. If uploads or saving fail, set folders  data/  and  assets/uploads/  to permission 755
     (or 775) in the File Manager.

FOLDER GUIDE
  index.php about.php services.php gallery.php contact.php news.php   - the 6 pages
  admin/        - management system
  includes/     - shared PHP code (header, footer, helpers)
  data/         - JSON "database" (news, gallery, slides, messages, settings, users)
  assets/css js img uploads

NOTES
  - All pictures in assets/img are your original supplied photos, unedited (no effects).
  - The sample news articles are editable drafts: please replace them with real, verified
    school news from the dashboard.
  - Contact form messages are stored in the dashboard (Messages). Free hosts usually block
    PHP mail(), so they are not emailed.
  - The map uses Google Maps embed with the text "Kyegobe, Fort Portal, Uganda"; change it
    in Settings if you want a different pin.
  - Colours used: green, gold yellow, grey and white only.
