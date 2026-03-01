import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';
import { Line } from 'react-chartjs-2';

export default function AuditLineComponent(audit_line_data) {

  function getDataset(index, data, labelColour) { 
    return { 
    label: index, 
    backgroundColor: labelColour,
    borderColor: "black",
    borderWidth: 2,
    data: [...data],
    showLine: true
    }
  }; 

    const lineData = JSON.parse(audit_line_data['audit_line_data']);
    //console.log(barData);
    // const labels =  ['Formal / educational','Other','Professional activities','Self-directed learning','Work Based Learning',];
    // const labels = ['EDU', 'Other', 'PRO', 'SDL', 'WBL'];
    // const labelColours = ["#50AF95","#f3ba2f"];

  const data = Array.isArray(lineData) ? getDataset("Monthly audited cases", lineData, "#50AF95") : getDataset("Monthly audited cases", [], "#50AF95") ;

    const [userData, setUserData] = useState({
        datasets: [data],
        options: {
          responsive: true,
        },
      });

      useEffect(() => {
        console.log(userData);
      }, [userData]);
    
      const [options, setOptions] = useState({
          responsive: true,
          scales: {
            x: {
              adapters: {
                date: {locale: 'en-GB'},
                type: 'date',
                distribution: 'linear',
                time: {
                  parser: 'yyyy-MM',
                  unit: 'month'
                },
                title: {
                  display: true,
                  text: 'Date'
                }
              }
            }
          }
        });
  
    return (
      <div className="ALC">
        {Array.isArray(lineData) &&
        <Line
          data={userData} 
          options={options}
          />
        }
      </div>
    );

}

if (document.getElementById('audit_line_component')) {
    const element = document.getElementById('audit_line_component');
    // create new props object with element's data-attributes
    const props = Object.assign({}, element.dataset);
    ReactDOM.render(<AuditLineComponent {...props}/>,
        document.getElementById('audit_line_component'));
}
