package captcha

import (
	"fmt"
	"net/http"
)

func DefaultHandler(w http.ResponseWriter, r *http.Request) {
	fmt.Fprintf(w, "captcha default at %s!", r.URL)
}

func ImageHandler(w http.ResponseWriter, r *http.Request) {
	fmt.Fprintf(w, "captcha image at %s!", r.URL)
}

func VerifyHandler(w http.ResponseWriter, r *http.Request) {
	fmt.Fprintf(w, "captcha verify at %s!", r.URL)
}
