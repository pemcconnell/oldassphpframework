Api parameters:-

1) api url: http://www.plsein.tk/api/webshots

2) post parameters:
   > params: array containing req. info about class file to be generated
		> url: url of webpage (e.g.: http://www.example.com)
		> height: optional height of image (only positive integer)
		> width: optional width of image (only positive integer)
		> fullpage: y means image will be of whole page and n means not
		> trim: y means it will try to remove extra white space surrounding the image
		> webshot: optional parameter to create webshot instead of guaranteed fullpage image
		> cropTop: optional parameter to crop image from top instead of center
	> ui: array containing user identification parameters
		> sec_code: value of secret code (will be available from profile page)
		> key: value of secret key (will be available from profile page)

3) response:
	> date: image content if image is generated
	> er: error information if any error occured during image creation

> Below example is just a demo for how to call api and use its out-put and you can even use its out-put in different way

> but we will recommend to call it from ajax, store image at their end once obtained from this api and use that image for multiple loading as direct loading will take much more time for the page to load.

> also almost every time image will be png but if you face problem where it gives jpeg image then please check image's mime type beforing storing / creating image st your end.

> due to caching some time, image same as that of previous call might be send on subsequent calls even if urls of webpage for which image is req. are different; to prevent this append random unique time based parameter to end of api url (as done in below example)

> In-case of any doubt, please feel free to contact us through contact page on our website http://www.plsein.tk/plsein/
