
## Structure

    	lib [community convention for server-side code]
    		db [or "models" if preferred, manages connection to DB and exposes models]
    		handler [application logic, actual implementation of the routes]
    		router [routes definition]
    		config [server configuration, could be a directory with multiple files in a more complex project]
    		server [creates and initializes the HTTP server]
    	public [unrestricted area]
    		css [stylesheets, could be plain CSS or preprocessor source files]
    		img [images and icons for the web app]
    		js [client-side javascript files]
    	test [community convention for automated unit test files]
    	views [templates for rendering of HTML pages, could be any Express-supported engine, Jade in this example]
    	Makefile [use "make" to run, "make test" to test, options available, see source]
    	app.js [dumb - no app logic - master file to assemble dependencies and start the app]
