/**
 * Utility to convert ISO 3166-1 alpha-2 country codes to flag CSS classes
 */
export function getCountryFlag(countryCode: string): string {
    if (!countryCode || countryCode.length !== 2) {
        console.log('Invalid country code:', countryCode);
        return 'fi fi-us'; // Default to US flag
    }

    // Convert ISO 3166-1 alpha-2 country code to flag-icons CSS class
    const upperCode = countryCode.toUpperCase();
    const flagClass = `fi fi-${upperCode.toLowerCase()}`;
    
    return flagClass;
}

export function useCountryFlag() {
    return { getCountryFlag };
}
