<?php

namespace App\Models;

use Carbon\Carbon;

class Nationality
{

    protected static $nationalities  = [];

    /**
     * Initialize the default Nationality.
     */
    protected static function initializeNationalities()
    {
        self::$nationalities  = [
            ['country' => 'Afghanistan', 'nationality' => 'Afghan', 'code' => 'AF'],
            ['country' => 'Albania', 'nationality' => 'Albanian', 'code' => 'AL'],
            ['country' => 'Algeria', 'nationality' => 'Algerian', 'code' => 'DZ'],
            ['country' => 'Andorra', 'nationality' => 'Andorran', 'code' => 'AD'],
            ['country' => 'Angola', 'nationality' => 'Angolan', 'code' => 'AO'],
            ['country' => 'Antigua and Barbuda', 'nationality' => 'Antiguan and Barbudan', 'code' => 'AG'],
            ['country' => 'Argentina', 'nationality' => 'Argentine', 'code' => 'AR'],
            ['country' => 'Armenia', 'nationality' => 'Armenian', 'code' => 'AM'],
            ['country' => 'Australia', 'nationality' => 'Australian', 'code' => 'AU'],
            ['country' => 'Austria', 'nationality' => 'Austrian', 'code' => 'AT'],
            ['country' => 'Azerbaijan', 'nationality' => 'Azerbaijani', 'code' => 'AZ'],
            ['country' => 'Bahamas', 'nationality' => 'Bahamian', 'code' => 'BS'],
            ['country' => 'Bahrain', 'nationality' => 'Bahraini', 'code' => 'BH'],
            ['country' => 'Bangladesh', 'nationality' => 'Bangladeshi', 'code' => 'BD'],
            ['country' => 'Barbados', 'nationality' => 'Barbadian', 'code' => 'BB'],
            ['country' => 'Belarus', 'nationality' => 'Belarusian', 'code' => 'BY'],
            ['country' => 'Belgium', 'nationality' => 'Belgian', 'code' => 'BE'],
            ['country' => 'Belize', 'nationality' => 'Belizean', 'code' => 'BZ'],
            ['country' => 'Benin', 'nationality' => 'Beninese', 'code' => 'BJ'],
            ['country' => 'Bhutan', 'nationality' => 'Bhutanese', 'code' => 'BT'],
            ['country' => 'Bolivia', 'nationality' => 'Bolivian', 'code' => 'BO'],
            ['country' => 'Bosnia and Herzegovina', 'nationality' => 'Bosnian and Herzegovinian', 'code' => 'BA'],
            ['country' => 'Botswana', 'nationality' => 'Botswanan', 'code' => 'BW'],
            ['country' => 'Brazil', 'nationality' => 'Brazilian', 'code' => 'BR'],
            ['country' => 'Brunei', 'nationality' => 'Bruneian', 'code' => 'BN'],
            ['country' => 'Bulgaria', 'nationality' => 'Bulgarian', 'code' => 'BG'],
            ['country' => 'Burkina Faso', 'nationality' => 'Burkinabe', 'code' => 'BF'],
            ['country' => 'Burundi', 'nationality' => 'Burundian', 'code' => 'BI'],
            ['country' => 'Cabo Verde', 'nationality' => 'Cape Verdean', 'code' => 'CV'],
            ['country' => 'Cambodia', 'nationality' => 'Cambodian', 'code' => 'KH'],
            ['country' => 'Cameroon', 'nationality' => 'Cameroonian', 'code' => 'CM'],
            ['country' => 'Canada', 'nationality' => 'Canadian', 'code' => 'CA'],
            ['country' => 'Central African Republic', 'nationality' => 'Central African', 'code' => 'CF'],
            ['country' => 'Chad', 'nationality' => 'Chadian', 'code' => 'TD'],
            ['country' => 'Chile', 'nationality' => 'Chilean', 'code' => 'CL'],
            ['country' => 'China', 'nationality' => 'Chinese', 'code' => 'CN'],
            ['country' => 'Colombia', 'nationality' => 'Colombian', 'code' => 'CO'],
            ['country' => 'Comoros', 'nationality' => 'Comorian', 'code' => 'KM'],
            ['country' => 'Congo', 'nationality' => 'Congolese', 'code' => 'CG'],
            ['country' => 'Congo (Democratic Republic)', 'nationality' => 'Congolese (Democratic Republic)', 'code' => 'CD'],
            ['country' => 'Costa Rica', 'nationality' => 'Costa Rican', 'code' => 'CR'],
            ['country' => 'Croatia', 'nationality' => 'Croatian', 'code' => 'HR'],
            ['country' => 'Cuba', 'nationality' => 'Cuban', 'code' => 'CU'],
            ['country' => 'Cyprus', 'nationality' => 'Cypriot', 'code' => 'CY'],
            ['country' => 'Czech Republic', 'nationality' => 'Czech', 'code' => 'CZ'],
            ['country' => 'Denmark', 'nationality' => 'Danish', 'code' => 'DK'],
            ['country' => 'Djibouti', 'nationality' => 'Djiboutian', 'code' => 'DJ'],
            ['country' => 'Dominica', 'nationality' => 'Dominican', 'code' => 'DM'],
            ['country' => 'Dominican Republic', 'nationality' => 'Dominican', 'code' => 'DO'],
            ['country' => 'Ecuador', 'nationality' => 'Ecuadorian', 'code' => 'EC'],
            ['country' => 'Egypt', 'nationality' => 'Egyptian', 'code' => 'EG'],
            ['country' => 'El Salvador', 'nationality' => 'Salvadoran', 'code' => 'SV'],
            ['country' => 'Equatorial Guinea', 'nationality' => 'Equatorial Guinean', 'code' => 'GQ'],
            ['country' => 'Eritrea', 'nationality' => 'Eritrean', 'code' => 'ER'],
            ['country' => 'Estonia', 'nationality' => 'Estonian', 'code' => 'EE'],
            ['country' => 'Eswatini', 'nationality' => 'Eswatini', 'code' => 'SZ'],
            ['country' => 'Ethiopia', 'nationality' => 'Ethiopian', 'code' => 'ET'],
            ['country' => 'Fiji', 'nationality' => 'Fijian', 'code' => 'FJ'],
            ['country' => 'Finland', 'nationality' => 'Finnish', 'code' => 'FI'],
            ['country' => 'France', 'nationality' => 'French', 'code' => 'FR'],
            ['country' => 'Gabon', 'nationality' => 'Gabonese', 'code' => 'GA'],
            ['country' => 'Gambia', 'nationality' => 'Gambian', 'code' => 'GM'],
            ['country' => 'Georgia', 'nationality' => 'Georgian', 'code' => 'GE'],
            ['country' => 'Germany', 'nationality' => 'German', 'code' => 'DE'],
            ['country' => 'Ghana', 'nationality' => 'Ghanaian', 'code' => 'GH'],
            ['country' => 'Greece', 'nationality' => 'Greek', 'code' => 'GR'],
            ['country' => 'Grenada', 'nationality' => 'Grenadian', 'code' => 'GD'],
            ['country' => 'Guatemala', 'nationality' => 'Guatemalan', 'code' => 'GT'],
            ['country' => 'Guinea', 'nationality' => 'Guinean', 'code' => 'GN'],
            ['country' => 'Guinea-Bissau', 'nationality' => 'Guinea-Bissauan', 'code' => 'GW'],
            ['country' => 'Guyana', 'nationality' => 'Guyanese', 'code' => 'GY'],
            ['country' => 'Philippines', 'nationality' => 'Filipino', 'code' => 'GY'],
        ];
    }

    /**
     * Get a list of holidays in the Philippines.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getNationalities()
    {
        if (empty(self::$nationalities)) {
            self::initializeNationalities();
        }
    
        // Debug line to check the contents after initialization
        return collect(self::$nationalities);
    }
}
