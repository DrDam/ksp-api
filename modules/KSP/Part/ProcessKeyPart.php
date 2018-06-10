<?php

namespace KSP;

class ProcessKeyPart
{

    public function getProcessors()
    {
        return [
            // Generics from Wiki
            'node_stack_top' => 'node_stack',
            'node_stack_top2' => 'node_stack',
            'node_stack_bottom'=> 'node_stack',
            'node_stack_bottom01'=> 'node_stack',
            'node_stack_bottom02'=> 'node_stack',
            'node_stack_bottom02'=> 'node_stack',
            'node_stack_bottom04'=> 'node_stack',
            'node_stack_connect1'=> 'node_stack',
            'node_stack_connect2'=> 'node_stack',
            'node_stack_connect3'=> 'node_stack',
            'node_stack_bottom2'=> 'node_stack',
            'node_stack_bottom2'=> 'node_stack',
            'node_stack_bottom3'=> 'node_stack',
            'node_stack_bottom4'=> 'node_stack',
            'node_stack_direct' => 'node_stack',
            
            'emission'=>'prefab_emission',
            'energy'=>'prefab_emission',
            'speed'=>'prefab_emission',
            'volume'=>'prefab_emission',
            'pitch'=>'prefab_emission',
            'localOffset'=>'local_offset',           
            
            'node_attach'=> 'node_stack',
            
            // Modules from Wiki
            'ejectDirection' => 'vector_offset',
            'center' => 'local_offset',
            'bogeyAxis' => 'vector_offset',
            'bogeyUpAxis' => 'vector_offset',
            'steeringCurve' => 'steering_curve',
            'torqueCurve' => 'torque_curve',
            'fx_gasBurst_white' => 'fx_gasBurst',
            'fx_exhaustFlame_blue' => 'fx_gasBurst',
            'fx_exhaustLight_blue' => 'fx_gasBurst',
            'fx_smokeTrail_light' => 'fx_gasBurst',
            'fx_exhaustSparks_flameout' => 'fx_gasBurst',
            'atmCurve' => 'atmosphere_curve_slope',
            'atmosphereCurve' => 'atmosphere_curve',
            'velCurve' => 'velocity_curve',
            'TemperatureModifier' => 'temperature_modifier',
            'ThermalEfficiency' => 'thermal_efficiency',
            
            // Not in wiki
            'CenterOfBuoyancy'=>'node_attach',
            'CenterOfDisplacement'=>'node_attach',
            'CoPOffset' => 'vector_offset',
            'CoLOffset' => 'vector_offset',
            'attachRules' => 'attach_rules',
            'fxOffset' => 'local_offset',
         //  ModuleColorChanger -> toggleInFlight
         //  DRAG_CUBE -> cube

            ];
    }

    public function node_stack($value)
    {
        $values = explode(',', $value);
        $headers = array('Position X', 'Position Y', 'Position Z', 'Angular X', 'Angular Y', 'Angular Z');

        if (count($values) > 6) {
            $headers[] = 'Size';
        }
        
        $this->complete($headers, $values);     
        
        return array_combine($headers, $values);
    }
    
    public function node_attach($value)
    {

        $values = explode(',', $value);
        $headers = array('stack', 'SrfAttach', 'allowStack', 'allowSrfAttach', 'allowCollision');

        $this->complete($headers, $values);      

        return array_combine($headers, $values);
    }
    
    public function vector_offset($value)
    {
        $values = explode(',', $value);
        $headers = array('X Direction', 'Y Direction', 'Z Direction');
 
        $this->complete($headers, $values);      

        return array_combine($headers, $values);
    }
    
    public function prefab_emission($value)
    {
        $values = explode(' ', $value);
        $headers = array('Throttle Range', 'Scale');

        $this->complete($headers, $values);      

        return array_combine($headers, $values);
    }
    
    public function temperature_modifier($value)
    {
        $values = explode(' ', $value);
        $headers = array('Temperature', 'Modifier');

        $this->complete($headers, $values);      

        return array_combine($headers, $values);
    }
    
    public function thermal_efficiency($value)
    {
        $values = explode(' ', $value);
        $headers = array('Temperature', 'Efficiency');

        $this->complete($headers, $values);      

        return array_combine($headers, $values);
    }
    public function local_offset($value)
    {
        $values = explode(',', $value);
        $headers = array('X', 'Y', 'Z');

        $this->complete($headers, $values);       

        return array_combine($headers, $values);
    }
    
    public function attach_rules($value)
    {

        $values = explode(',', $value);
        $headers = array('Stack', 'Surface Attach', 'Allow Stack', 'Allow Surface Attach', 'Allow Collision');

        $this->complete($headers, $values); 

        return array_combine($headers, $values);
    }

    public function fx_gasBurst($value)
    {
        $values = explode(',', $value);
        $headers = array('Position X', 'Position Y', 'Position Z', 'Angular X', 'Angular Y', 'Angular Z', 'activate');

        if (count($values) > 7) {
            $headers[] = 'deactivate';
        }
        
        $this->complete($headers, $values);     
        
        return array_combine($headers, $values);
    }
    
    public function atmosphere_curve($value)
    {
        $values = explode(' ', $value);
        $headers = array('Atmospher', 'ISP');
        
        $this->complete($headers, $values);   
        
        return array_combine($headers, $values);
    }
    
    public function atmosphere_curve_slope($value)
    {
        $values = explode(' ', $value);
        $headers = array('Atmospher', 'ISP_factor','slope left','slope right' );
        $this->complete($headers, $values);   
        
        return array_combine($headers, $values);
    }
    
    public function velocity_curve($value)
    {

        $values = explode(' ', $value);
        $headers = array('Velocity', 'thrust_factor','slope left','slope right' );
        $this->complete($headers, $values);   

        return array_combine($headers, $values);
    }

    public function steering_curve($value)
    {

        $values = explode(' ', $value);
        $headers = array('Speed', 'Angle');

        $this->complete($headers, $values); 
        
        return array_combine($headers, $values);
    }

    public function torque_curve($value)
    {
        $values = explode(' ', $value);
        $headers = array('Speed', 'Torque X', 'Torque Y', 'Torque Z');
        
        $this->complete($headers, $values); 
        
        return array_combine($headers, $values);
    }

    
    private function complete(&$headers, &$values) {
        $this->completeSize($headers, count($values));
        $this->completeSize($values, count($headers));  
    }
    
    private function completeSize(&$array, $size) {
        $i = 0;
        while ($size > count($array)) {
            $i++;
            $array[] = '+'.$i;
        }
    }
}
