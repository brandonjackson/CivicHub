If you want add custom code to the plugin, styles, or other such things,
you should avoid changing core code at all costs. As such, gPress checks
the "custom" folder in the root of the "gpress" plugin for 3 files.

custom.php
custom.css
custom.js

It then loads these files (if they exist) at the end of everything else,
but equally within their own respective places...