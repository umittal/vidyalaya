// A utility function that returns true if a string contains only
// whitespace characters.
function isblank(e)
{
  if (e.value == null || e.value == "")
    return true;

  for(var i = 0; i < e.value.length; i++)
  {
     var c = e.value.charAt(i);
     if ((c != ' ') &&
         (c != '\n') &&
         (c != '\t'))
        return false;
  }
  return true;
}

// Checks if an optional field is blank
function checkblank(e)
{
  if (isblank(e))
  {
    alert("The field " + e.description + " must be filled in.");
    return false;
  }
  return true;
}

// Checks if a field is numeric.
// If the optional min property is set, it checks it is greater than
// its value
// If the optional max property is set, it checks it is less than
// its value
function checknumber(e)
{
  var v = parseFloat(e.value);

  if (isNaN(v))
  {
    alert("The field " + e.description + " must be a number");
    return false;
  }

  if ((e.minNumber != null) && (v < e.minNumber))
  {
    alert("The field " + e.description +
          " must be greater than or equal to " + e.minNumber);
    return false;
  }

  if (e.maxNumber != null && v > e.maxNumber)
  {
    alert("The field " + e.description +
          " must be less than or equal to " + e.maxNumber);
    return false;
  }

  return true;
}

// Checks if a field looks like a date in the 99/99/9999 format
function checkdate(e)
{
  var slashCount = 0;
  if (e.value.length != 10)
  {
    alert(" The field " + e.description +
          " must have the format 99/99/9999" +
          " and be 10 characters in length");
    return false;
  }

  for(var j = 0; j < e.value.length; j++)
  {
    var c = e.value.charAt(j);

    if ((c == '/'))
       slashCount++;

    if (c != '/' && (c < '0' || c > '9'))
    {
      alert(" The field " + e.description +
            " can contain only numbers and forward-slashes");
      return false;
    }
  }

  if (slashCount != 2)
  {
    alert(" The field " + e.description +
          " must have the format 99/99/9999");
    return false;
  }

  return true;
}

// Checks if a field contains any whitespace
function checkwhitespace(e)
{
  var seenAt = false;

  for(var j = 0; j < e.value.length; j++)
  {
     var c = e.value.charAt(j);

     if ((c == ' ') || (c == '\n') || (c == '\t'))
     {
       alert("The field " + e.description +
             " must not contain whitespace");
       return false;
     }
  }
  return true;
}

// Now check for fields that are supposed to be emails.
// Only checks that there's one @ symbol and no whitespace
function checkemail(e)
{
  var seenAt = false;

  for(var j = 0; j < e.value.length; j++)
  {
    var c = e.value.charAt(j);

    if ((c == ' ') || (c == '\n') || (c == '\t'))
    {
      alert("The field " + e.description + 
            " must not contain whitespace");
      return false;
    }

    if ((c == '@') && (seenAt == true))
    {
      alert("The field " + e.description + " must contain only one @");
      return false;
    }

    if ((c == '@'))
      seenAt = true;
  }

  if (seenAt == false)
  {
    alert("The field " + e.description + " must contain one @");
    return false;
  }
  return true;
}

// This is the function that performs <form> validation.
// It is invoked from the onSubmit( ) event handler.
// The handler should return whatever value this function
// returns.
function verify(f)
{
  // Loop through the elements of the form, looking for all
  // text and textarea elements. Report errors using a post validation,
  // field-by-field approach
  for(var i = 0; i < f.length; i++)
  {
     var e = f.elements[i];

     if (((e.type == "text") || (e.type == "textarea")))
     {
        // first check if the field is empty and shouldn't be
        if (!e.isOptional && !checkblank(e))
          return false;

        // Now check for fields that are supposed to be numeric.
        if (!isblank(e) && e.isNumeric && !checknumber(e))
          return false;

        // Now check for fields that are supposed to be dates
        if (!isblank(e) && e.isDate && !checkdate(e))
          return false;

        // Now check for fields that are supposed to be emails
        if (!isblank(e) && e.isEmail && !checkemail(e))
          return false;

        // Now check for fields that are supposed
        // not to have whitespace
        if (!isblank(e) && e.hasNospaces && !checkwhitespace(e))
          return false;
     } // if (type is text or textarea)
  } // for each character in field

  // There were no errors if we got this far
  return true;
}

