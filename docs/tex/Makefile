MASTER = lancms

# make all clean er tingen



#all: $(MASTER).dvi $(MASTER).ps $(MASTER).pdf
all: $(MASTER).pdf

#$(MASTER).dvi: *.tex 
#	@latex $(MASTER).tex
#	@if grep 'LaTeX Warning: Label(s) may have changed' $(MASTER).log \
#		>/dev/null ; then latex $(MASTER).tex ; else true ; fi

#%.ps: %.dvi
#	dvips $< -o

$(MASTER).pdf: *.tex
	pdflatex $(MASTER).tex

force:
	touch $(MASTER).tex

tags:
	etags *.tex

clean:
	rm -rf *~ *.aux *.bbl *.blg *.dvi *.idx *.xyc TAGS \
		*.ilg *.ind *.log *.toc *.lof *.lot \#*\# *.2.ps
