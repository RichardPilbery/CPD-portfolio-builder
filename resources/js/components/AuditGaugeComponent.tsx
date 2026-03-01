import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom';
import { Doughnut } from 'react-chartjs-2';

interface AuditGaugeProps {
  value: number, 
  max: number
};

const AuditGaugeComponent: React.FC<AuditGaugeProps> = (props)  => {
  const percent = props.value / props.max;
  console.log('Percent is ' + percent);
  const labels = ['Vascular access success'];
  const labelColours = [];

    const [userData,] = useState({
      datasets: [
        {
          label: "Vascular access success",
          backgroundColor: [
            'rgb(255, 99, 132)',
            '#ccc',
          ],
          data: [
            percent * 100,
            100 - (percent * 100),
          ],
        },
      ],
    });
    
      const [options, ] = useState({
        rotation: 270, // start angle in radians
        circumference: 180, // sweep angle in radians
        maintainAspectRatio: false,
        responsive: true
      });


  return <Doughnut
    data={userData}
    options={options}
  />
};

export default AuditGaugeComponent;

 

if (document.getElementById('audit_gauge_component')) {
    const element = document.getElementById('audit_gauge_component');
    // create new props object with element's data-attributes
    const props = Object.assign({}, element.dataset);
    ReactDOM.render(<AuditGaugeComponent {...props}/>,
        document.getElementById('audit_gauge_component'));
}

if (document.getElementById('audit_gauge_component2')) {
  const element = document.getElementById('audit_gauge_component2');
  // create new props object with element's data-attributes
  const props = Object.assign({}, element.dataset);
  ReactDOM.render(<AuditGaugeComponent {...props}/>,
      document.getElementById('audit_gauge_component2'));
}