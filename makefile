version = 0_0_00
outfile = Oxid6_nl2go_$(version).zip

$(version): $(outfile)

$(outfile):
	mkdir newsletter2go
	cp -r ./Controller newsletter2go
	cp -r ./Core newsletter2go
	cp -r ./Helper newsletter2go
	cp -r ./Model newsletter2go
	cp -r ./Screenshots newsletter2go
	cp -r ./translations newsletter2go
	cp -r ./views newsletter2go
	cp ./composer.json newsletter2go
	cp ./metadata.php newsletter2go
	cp ./readme.md newsletter2go
	cp ./picture.png newsletter2go
	zip -r  build.zip ./newsletter2go/*
	mv build.zip $(outfile)
	rm -r newsletter2go
clean:
	rm -rf newsletter2go
