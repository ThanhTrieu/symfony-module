CMD /C concat ^
    css/reset.css ^
    css/define.css ^
    css/style.css ^
    css/fontello.css ^
    css/custom.css ^
    -o css/gurugamer.min.css
CMD /C cssnano css/gurugamer.min.css css/gurugamer.min.css