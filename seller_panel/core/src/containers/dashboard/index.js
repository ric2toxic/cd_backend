import React, { Component } from 'react'
import Card, { CardHeader, CardContent } from 'material-ui/Card';

class Dashboard  extends Component {

 render(){ 

 return(<Card>
          <CardHeader title="Dashboard" />
          <CardContent>
            <h4> Welcome to seller panel </h4> 
           </CardContent> 
        </Card>
)
}}


export default Dashboard;