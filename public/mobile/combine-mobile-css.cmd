CMD /C concat ^
    css/reset.css ^
    css/define.css ^
    css/style.css ^
    css/fontello.css ^
    css/homegallery/lightslider.css ^
    css/custom.css ^
    -o css/gurugamer-mobile.min.css
CMD /C cssnano css/gurugamer-mobile.min.css css/gurugamer-mobile.min.css