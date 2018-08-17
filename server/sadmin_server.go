package main

import (
	"fmt"
	"log"
	"net/http"
	"flag"
	"github.com/gorilla/websocket"
)
var addr = flag.String("addr", ":5000", "http service address")

var upgrader = websocket.Upgrader{
	ReadBufferSize: 1024,
	WriteBufferSize: 1024,
	EnableCompression: true,
	CheckOrigin: func(r *http.Request) bool {
		return true
	},
} // use default options

func home(w http.ResponseWriter, r *http.Request) {
	fmt.Println("进来HOME了!");
	c, err := upgrader.Upgrade(w, r, nil)
	if err != nil {
		log.Print("upgrade:", err)
		return
	}
	defer c.Close()
	for {
		fmt.Println("等待新数据...");
		mt, message, err := c.ReadMessage()
		if err != nil {
			log.Println("read:", err)
			break
		}
		log.Printf("recv: %s", message)
		err = c.WriteMessage(mt, message)
		if err != nil {
			log.Println("write:", err)
			break
		}
	}
}

func main() {
	flag.Parse();
	fmt.Printf("%v", addr);
	log.SetFlags(0)
	http.HandleFunc("/", home);
	log.Fatal(http.ListenAndServe(*addr, nil))
}