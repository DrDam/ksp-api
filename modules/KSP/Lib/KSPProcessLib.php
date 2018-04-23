<?php

namespace KSP;

class KSPProcessLib
{

    public function getProcessors()
    {
        return [
            'node_attach' => 'node_attach',
            'node_stack_direct' => 'node_attach',
            'node_stack_bottom' => 'node_attach',
            'node_stack_top' => 'node_attach',
            
            'CoPOffset' => 'vector_offset',
            'CoLOffset' => 'vector_offset',
            'CenterOfBuoyancy'=>'node_attach',
            'CenterOfDisplacement'=>'node_attach',
            
            'attachRules' => 'attach_rules',
            
            'atmosphereCurve' => 'atmosphere_curve',
            
            'velocityCurve' => 'velocity_curve',
            
            'powerCurve' => 'power_curve',
            
            'steeringCurve' => 'steering_curve',
            
            'torqueCurve' => 'torque_curve',
            
            'velCurve' => 'jet_velocity_curve',
            'atmCurve' => 'jet_atmo_curve',
            ];
    }

    public function node_attach($value)
    {

        $values = explode(',', $value);
        $headers = array('Position X', 'Position Y', 'Position Z', 'Angular X', 'Angular Y', 'Angular Z');

        if (count($values) == 7) {
            $headers[] = 'Size';
        }
        
        while (count($values) > count($headers)) {
            $headers[] = 'OUT';
        }
        while (count($headers) > count($values)) {
            $values[] = '';
        }

        return array_combine($headers, $values);
    }
    
    public function vector_offset($value)
    {
        $values = explode(',', $value);
        $headers = array('X Direction', 'Y Direction', 'Z Direction');
        
        while (count($values) > count($headers)) {
            $headers[] = 'OUT';
        }

        return array_combine($headers, $values);
    }

    public function attach_rules($value)
    {

        $values = explode(',', $value);
        $headers = array('Stack', 'Surface Attach', 'Allow Stack', 'Allow Surface Attach', 'Allow Collision');

        if (count($values) == 7) {
            $headers[] = '+1';
            $headers[] = '+2';
        }

        return array_combine($headers, $values);
    }

    public function atmosphere_curve($value)
    {

        $values = explode(' ', $value);
        $headers = array('Atmospher', 'ISP');
        if (count($values) == 4) {
            $headers[] = '+1';
            $headers[] = '+2';
        }
        return array_combine($headers, $values);
    }

    public function velocity_curve($value)
    {

        $values = explode(' ', $value);
        $headers = array('Velocity', '% thrust Max');

        if (count($values) == 4) {
            $headers[] = 'slope left';
            $headers[] = 'slope right';
        }

        while (count($values) > count($headers)) {
            $headers[] = '';
        }
        while (count($headers) > count($values)) {
            $values[] = '';
        }
        
        return array_combine($headers, $values);
    }

    public function power_curve($value)
    {

        $values = explode(' ', $value);
        $headers = array('Dist to kerbol', 'Multiplicator', '', '');

        return array_combine($headers, $values);
    }

    public function steering_curve($value)
    {

        $values = explode(' ', $value);
        $headers = array('Speed', 'Angle');

        return array_combine($headers, $values);
    }

    public function torque_curve($value)
    {
        $values = explode(' ', $value);
        $headers = array('Speed', 'Torque X', 'Torque Y', 'Torque Z');
        
        while (count($values) > count($headers)) {
            $headers[] = '';
        }
        while (count($headers) > count($values)) {
            $values[] = '';
        }
        
        return array_combine($headers, $values);
    }
    
    public function jet_velocity_curve($value)
    {
        return $value;
    }
    public function jet_atmo_curve($value)
    {
        return $value;
    }
}
