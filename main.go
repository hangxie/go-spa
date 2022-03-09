package main

import (
	"fmt"
	"log"
	"net/http"
	"os"
	"strings"

	"github.com/alecthomas/kong"
	"github.com/hangxie/go-spa/captcha"
	"github.com/hangxie/go-spa/mapper"
	"github.com/hangxie/go-spa/static"
)

var cli struct {
	Prefix  string `help:"Prefix of serving URL" default:"/"`
	Address string `help:"IP address of HTTP server" default:"127.0.0.1"`
	Port    int    `help:"Port of HTTP server" default:"8080"`
}

var routes = map[string]func(http.ResponseWriter, *http.Request){
	"/captcha":        captcha.DefaultHandler,
	"/captcha/image":  captcha.ImageHandler,
	"/captcha/verify": captcha.VerifyHandler,
	"/map":            mapper.DefaultHandler,
	"/static/":        static.DefaultHandler,
}

func main() {
	parser := kong.Must(
		&cli,
		kong.UsageOnError(),
		kong.ConfigureHelp(kong.HelpOptions{Compact: true}),
		kong.Description("go single page application"),
	)

	_, err := parser.Parse(os.Args[1:])
	parser.FatalIfErrorf(err)

	if !strings.HasPrefix(cli.Prefix, "/") {
		cli.Prefix = "/" + cli.Prefix
	}
	cli.Prefix = strings.TrimRight(cli.Prefix, "/")

	for pattern, handler := range routes {
		http.HandleFunc(cli.Prefix+pattern, handler)
	}
	addr := fmt.Sprintf("%s:%d", cli.Address, cli.Port)
	if err := http.ListenAndServe(addr, nil); err != nil {
		log.Fatal(err)
	}
}
