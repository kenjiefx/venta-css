const express = require ('express')
const app = express()
const fs = require ('fs')
const port = 3000

fs.readFile('venta.config.json','utf-8',(err,data)=>{
  if (err) {
    app.use((req,res)=>{
        res.status(500)
        res.json({error:'Config not found! Run the <hook> command to instantiate Venta application'})
    })
    return
  }
  const config = JSON.parse(data)
  app.use(express.static('test'))

  app.get('/logs/:resource',function(req,res){
    fs.readFile(`vnt/${config.namespace}/venta/__venta.${req.params.resource}`,'utf8',(err,data)=>{
      if (err) {
        res.status(500)
        res.json({'error':'Resource not found! Run the <build> command or <hook> if you have not started the application.'})
        return
      }
      res.json(JSON.parse(data))
      return
    })
  });

  // When resource is not found
  app.use((req,res)=>{
    res.status(404)
    res.json({error:'Resource not found'})
  })

})

// app.use(express.static('test'))
//
// app.get('/logs/css.json',function(req,res){
//     fs.readFile('/vnt/joe/test.txt', 'utf8' , (err, data) => {
//       if (err) {
//         res.json({'error':'css.json not found! Run the <build> command or <hook> if you have not started the application.'})
//         return
//       }
//       console.log(data)
//     })
// });
//
// app.use(function(req, res){
//     res.status(404)
//     res.send("Not Found")
// })

app.listen(port, () => {
  console.log(`Example app listening at http://localhost:${port}`)
})
