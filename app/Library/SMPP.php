<?php


namespace App\Library;


class SMPP
{


    public $socket;
    public $seq = 0;
    public $debug = 0;
    public $data_coding = 0;
    public $timeout = 2;

    /**
     * @param $id
     * @param $data
     *
     * @return array|false|string
     */
    function send_pdu($id, $data)
    {
        if ($this->socket == false) {
            return "Send PDU: Connection closed!";
        }

        // increment sequence
        $this->seq += 1;
        // PDU = PDU_header + PDU_content
        $pdu = pack('NNNN', strlen($data) + 16, $id, 0, $this->seq).$data;
        // send PDU

        fputs($this->socket, $pdu);

        // Get response length
        $data = fread($this->socket, 4);
        if ($data == false) {
            return "Send PDU: Connection closed!";
        }
        $tmp = unpack('Nlength', $data);

        $command_length = $tmp['length'];
        if ($command_length < 12) {
            return false;
        }

        // Get response
        $data = fread($this->socket, $command_length - 4);
        $pdu  = unpack('Nid/Nstatus/Nseq', $data);
        if ($this->debug) {
            print "\n< R PDU (id,status,seq): ".join(" ", $pdu);
        }

        return $pdu;
    }


    /**
     * @param $host
     * @param $port
     * @param $system_id
     * @param $password
     *
     * @return bool|string
     */
    function open($host, $port, $system_id, $password)
    {

        // Open the socket

        $this->socket = @fsockopen($host, $port, $errno, $err_str, $this->timeout);

        if ($this->socket == false) {
            return "$err_str ($errno)<br />";
        }
        if (function_exists('stream_set_timeout')) {
            stream_set_timeout($this->socket, $this->timeout);
        } // function exists for php4.3+
        if ($this->debug) {
            print "\n> Connected";
        }


        // Send Bind operation
        $data = sprintf("%s\0%s\0", $system_id, $password); // system_id, password
        $data .= sprintf("%s\0%c", "", 0x34);  // system_type, interface_version
        $data .= sprintf("%c%c%s\0", 5, 0, ""); // addr_ton, addr_npi, address_range

        $ret = $this->send_pdu(2, $data);
        if ($this->debug) {
            print "\n> Bind done!";
        }

        if (is_array($ret) && array_key_exists('status', $ret)){
            return ($ret['status'] == 0);
        }

        print "\n> Please check your server firewall";

        return false;
    }

    /**
     * @param $source_addr
     * @param $destination_addr
     * @param $short_message
     * @param  string  $optional
     *
     * @return bool
     */
    function submit_sm($source_addr, $destination_addr, $short_message, $optional = ''): bool
    {

        $data = sprintf("%s\0", ""); // service_type
        $data .= sprintf("%c%c%s\0", 5, 0, $source_addr); // source_addr_ton, source_addr_npi, source_addr
        $data .= sprintf("%c%c%s\0", 1, 1, $destination_addr); // dest_addr_ton, dest_addr_npi, destination_addr
        $data .= sprintf("%c%c%c", 0, 0, 0); // esm_class, protocol_id, priority_flag
        $data .= sprintf("%s\0%s\0", "", ""); // schedule_delivery_time, validity_period
        $data .= sprintf("%c%c", 0, 0); // registered_delivery, replace_if_present_flag
        $data .= sprintf("%c%c", $this->data_coding, 0); // data_coding, sm_default_msg_id
        $data .= sprintf("%c%s", strlen($short_message), $short_message); // sm_length, short_message
        $data .= $optional;

        $ret = $this->send_pdu(4, $data);
        if (isset($ret['status'])) {
            return ($ret['status'] == 0);
        }

        return false;

    }

    /**
     * @return bool|string
     */
    function close()
    {
        if ($this->socket == false) {
            return "Send PDU: Connection closed!";
        }

        $this->send_pdu(6, "");
        fclose($this->socket);

        return true;
    }


    /**
     * @param $source_addr
     * @param $destination_addr
     * @param $short_message
     * @param  int  $utf
     * @param  int  $flash
     *
     * @return bool
     */
    function send_long($source_addr, $destination_addr, $short_message, $utf = 0, $flash = 0): bool
    {

        if ($utf) {
            $this->data_coding = 0x08;
        }

        if ($flash) {
            $this->data_coding = $this->data_coding | 0x10;
        }


        $size = strlen($short_message);
        if ($utf) {
            $size += 20;
        }

        if ($size < 160) { // Only one part :)
            $this->submit_sm($source_addr, $destination_addr, $short_message);

        } else { // Multipart
            $sar_msg_ref_num    = rand(1, 255);
            $sar_total_segments = ceil(strlen($short_message) / 130);

            for ($sar_segment_seq_num = 1; $sar_segment_seq_num <= $sar_total_segments; $sar_segment_seq_num++) {
                $part          = substr($short_message, 0, 130);
                $short_message = substr($short_message, 130);

                $optional = pack('nnn', 0x020C, 2, $sar_msg_ref_num);
                $optional .= pack('nnc', 0x020E, 1, $sar_total_segments);
                $optional .= pack('nnc', 0x020F, 1, $sar_segment_seq_num);

                if ($this->submit_sm($source_addr, $destination_addr, $part, $optional) === false) {
                    return false;
                }

            }
        }

        return true;

    }

}
