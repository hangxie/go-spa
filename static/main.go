package static

import (
	"fmt"
	"net/http"
)

func DefaultHandler(w http.ResponseWriter, r *http.Request) {
	fmt.Fprintf(w, "map default at %s!", r.URL)
}
