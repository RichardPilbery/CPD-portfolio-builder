import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';
import { Bar } from 'react-chartjs-2';

export default function PortfolioTypesBarComponent(portfolio_bar_data) {

  function getDataset(index, data, labelColour) { 
    return { 
    label: index, 
    backgroundColor: labelColour,
    borderColor: "black",
    borderWidth: 2,
    data: data 
    }
  }; 

  
  const barData = JSON.parse(portfolio_bar_data['portfolio_bar_data']);
  console.log(barData);
  const data = [];
  // const labels =  ['Formal / educational','Other','Professional activities','Self-directed learning','Work Based Learning',];
  // const labels = ['EDU', 'Other', 'PRO', 'SDL', 'WBL'];
  const labels = ['WBL', 'PRO', 'EDU', 'SDL', 'Other'];
  const labelColours = ["#50AF95","#f3ba2f"];

  let i = 0;
  for (const [key, value] of Object.entries(barData)) {
    // console.log('key is ' + key + ' and value is ' + value);
    if (Array.isArray(value)) {
      var totals = [];
      for (const [k, v] of Object.entries(value)) {
        //console.log(k);
        //console.log('Abbreviation is ' + v.abbr + ' and total = ' + v.total);
        switch(v.abbr) {
          case 'WBL':
            totals[0] = v.total;
            // console.log('WBL and totals: ' + totals[0]);
            break;
          case 'PRO':
            totals[1] = v.total;
            break;     
          case 'EDU':
            totals[2] = v.total;
            break;  
          case 'SDL':
            totals[3] = v.total;
            break;
          default:
            totals[4] = v.total;         
        }
      }
      // const totals = value.map(e => e.total);
      // console.log('totals');
      data.push(getDataset(key, totals, labelColours[i]));
      i++;
    }
  }

    const [userData, setUserData] = useState({
        labels: labels,
        respsonsive: true,
        datasets: data
      });
    
      const [options, setOptions] = useState({
          responsive: true,
          scales: {
            x: {
              stacked: true,
            },
            y: {
              stacked: true,
            },
          },
        });
  
    return (
      <div className="TBC">
        <Bar 
          data={userData} 
          options={options}
          />
      </div>
    );

}

if (document.getElementById('portfolio_types_bar_component')) {
    const element = document.getElementById('portfolio_types_bar_component');
    // create new props object with element's data-attributes
    const props = Object.assign({}, element.dataset);
    ReactDOM.render(<PortfolioTypesBarComponent {...props}/>,
        document.getElementById('portfolio_types_bar_component'));
}
